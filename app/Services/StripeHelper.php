<?php

namespace App\Services;

use App\Exceptions\SessionException;
use App\Models\CardHolder;
use App\Models\CreditCard;
use Stripe\StripeClient;

class StripeHelper
{
    private $stripeClient;

    public function __construct()
    {
        if (!env('STRIPE_API_KEY')) {
            return false;
        }
        $this->stripeClient = new StripeClient(env('STRIPE_API_KEY'));
    }
    public function storeCardHolder($user)
    {
        if (!env('STRIPE_API_KEY')) {
            throw new SessionException('.env STRIPE_API_KEY cannot be empty');
        }
        if (request('cardholder_address') == 'app') {
            $cardholder_data = get_option('cardholder_data');
            $address = [
                'line1' => $cardholder_data['address_line'],
                'city' => $cardholder_data['city'],
                'state' => $cardholder_data['state'],
                'postal_code' => $cardholder_data['postal_code'],
                'country' => $cardholder_data['country'],
            ];
        }
        if (request('cardholder_address') == 'user') {
            $address = [
                'line1' => $user->address_line,
                'city' => $user->city->name,
                'state' => $user->state->name,
                'postal_code' => $user->postal_code,
                'country' => $user->country->code,
            ];
        }

        $cardholder = $this->stripeClient->issuing->cardholders->create([
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => request('cardholder_address') == 'app' ? $cardholder_data['phone'] : $user->phone,
            'status' => 'active',
            'type' => 'individual',
            'individual' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'dob' => ['day' => $user->dob_day, 'month' => $user->dob_month, 'year' => $user->dob_year],
            ],
            'billing' => [
                'address' => $address,
            ],
        ]);

        if ($cardholder->created) {
            $newCardHolder =   CardHolder::create([
                'user_id' => $user->id,
                'cardholder' => $cardholder->id,
                'status' => $cardholder->status,
            ]);
        }
        return $newCardHolder ?? $cardholder ?? [];
    }

    public function storeCreditCard($cardholder, $order)
    {
        if (!env('STRIPE_API_KEY')) {
            throw new SessionException('.env STRIPE_API_KEY cannot be empty');
        }
        $orderedCard = $order->card;
        $card = $this->stripeClient->issuing->cards->create([
            'cardholder' => $cardholder->cardholder,
            'currency' => env('STRIPE_CURRENCY', 'usd'),
            'type' => 'virtual',
            "spending_controls" => [
                "allowed_categories" => $orderedCard?->category_type === 'allowed' ? $orderedCard->categories : null,
                "blocked_categories" => $orderedCard?->category_type === 'blocked' ? $orderedCard->categories : null,
                'spending_limits' => [
                    [
                        'amount' => 1,
                        'interval' => 'all_time',
                    ],
                ],
            ],
        ]);
        if ($card->created) {
            CreditCard::create([
                'card' => $card->id,
                'user_id' => $order->user->id,
                'card_holder_id' => $cardholder->id,
                'card_id' => $order->card->id,
                'card_order_id' => $order->id,
                'status' => $card->status,
                'exp_month' => $card->exp_month,
                'exp_year' => $card->exp_year,
                'last4' => $card->last4,
                'address_type' => request('cardholder_address'),
                'currency' => env('STRIPE_CURRENCY', 'usd'),
            ]);
            $this->acceptTerms($order, $cardholder->cardholder);
        }
        return $card;
    }

    public function currentSpend($card_id)
    {
        if (!env('STRIPE_API_KEY')) {
            throw new SessionException('.env STRIPE_API_KEY cannot be empty');
        }
        $totalSpend = 0;
        try {
            $totalSpend = $this->fetchTransactions($card_id);
        } catch (\Exception $e) {
            // Handle exception
        }
        return $totalSpend;
    }

    private function fetchTransactions($card_id, $starting_after = null)
    {
        $startOfMonth = strtotime("first day of this month midnight");
        $endOfMonth = strtotime("first day of next month midnight");
        $params = [
            'limit' => 100,
            'created' => [
                'gte' => $startOfMonth,
                'lt' => $endOfMonth
            ],
            'card' => $card_id
        ];
        if ($starting_after) {
            $params['starting_after'] = $starting_after;
        }

        $transactions = $this->stripeClient->issuing->transactions->all($params);

        $totalSpend = 0;
        foreach ($transactions->data as $transaction) {
            $totalSpend += $transaction->amount;
        }

        if ($transactions->has_more) {
            $totalSpend += $this->fetchTransactions($card_id,  end($transactions->data)->id);
        }

        return $totalSpend / 100;
    }

    public function transactions($routeName, $cardholder = null, $card = null, $limit = 15)
    {
        if (!env('STRIPE_API_KEY')) {
            return [];
        }
        $starting_after = request()->input('starting_after');
        $params = ['limit' => $limit];
        if ($cardholder) {
            $params['cardholder'] = $cardholder;
        } elseif ($card) {
            $params['card'] = $card;
        }

        if ($starting_after) {
            $params['starting_after'] = $starting_after;
        }

        $transactions = $this->stripeClient->issuing->transactions->all($params);

        $has_more = $transactions->has_more;
        $next = $has_more
            ? route($routeName, ['starting_after' => end($transactions->data)->id])
            : null;
        $prev = $starting_after
            ? route($routeName, ['ending_before' => $transactions->data[0]->id])
            : null;

        return [
            'transactions' => $transactions,
            'has_more' => $has_more,
            'next' => $next,
            'prev' => $prev,
        ];
    }

    public function authorizations($routeName, $cardholder = null, $card = null, $limit = 15)
    {
        if (!env('STRIPE_API_KEY')) {
            return [];
        }
        $starting_after = request()->input('starting_after');
        $params = ['limit' => $limit];
        if ($cardholder) {
            $params['cardholder'] = $cardholder;
        } elseif ($card) {
            $params['card'] = $card;
        }
        if ($starting_after) {
            $params['starting_after'] = $starting_after;
        }

        $authorizations = $this->stripeClient->issuing->authorizations->all($params);

        $has_more = $authorizations->has_more;
        $next = $has_more
            ? route($routeName, ['starting_after' => end($authorizations->data)->id])
            : null;
        $prev = $starting_after
            ? route($routeName, ['ending_before' => $authorizations->data[0]->id])
            : null;

        return [
            'authorizations' => $authorizations,
            'has_more' => $has_more,
            'next' => $next,
            'prev' => $prev,
        ];
    }

    public function updateCardStatus($creditCardId, $status = 'inactive')
    {
        if (!env('STRIPE_API_KEY')) {
            throw new SessionException('.env STRIPE_API_KEY cannot be empty');
        }
        $this->stripeClient->issuing->cards->update(
            $creditCardId,
            ['status' => $status]
        );;
    }

    public function updateSpendingLimits($creditCardId, $amount)
    {
        if (!env('STRIPE_API_KEY')) {
            throw new SessionException('.env STRIPE_API_KEY cannot be empty');
        }
        $amount = $amount ?? request('amount');
        $card = $this->stripeClient->issuing->cards->update(
            $creditCardId,
            [
                'spending_controls' => [
                    'spending_limits' => [
                        [
                            'amount' => $amount * 100,
                            'interval' => 'all_time',
                        ],
                    ],
                ],
            ]
        );

        return [
            'spending_limits' => $card['spending_controls']['spending_limits'][0]['amount'],
        ];
    }

    public function acceptTerms($order, $cardholder)
    {
        $this->stripeClient->issuing->cardholders->update(
            $cardholder,
            [
                'individual' => [
                    'card_issuing' => [
                        'user_terms_acceptance' => [
                            'date' => time(),
                            'ip' => $order->ip_address ?? '91.121.146.224',
                        ]
                    ]
                ]
            ]
        );
    }
}
