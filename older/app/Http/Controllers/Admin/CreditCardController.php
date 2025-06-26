<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Services\StripeHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Notification;
use App\Helpers\CardHelper;
use App\Models\Gateway;
use App\Models\CardHolder;
use App\Models\CardTopup;
use App\Models\OtherCity;
use App\Models\OtherState;

class CreditCardController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:credit-card');
    }
    public function index(Request $request)
    {
        $creditCards = CreditCard::query()
            ->filterOn(['title', 'last4', 'status'])
            ->with(['user', 'card_order'])
            ->latest()
            ->paginate(12);

        $totalCards = CreditCard::count();
        $totalPendingCards = CreditCard::where('status', 'pending')->count();
        $totalApprovedCards = CreditCard::where('status', 'approved')->count();
        $totalRejectedCards = CreditCard::where('status', 'rejected')->count();
        return Inertia::render('Admin/CreditCard/Index', [
            'creditCards' => $creditCards,
            'totalCards' => $totalCards,
            'totalPendingCards' => $totalPendingCards,
            'totalApprovedCards' => $totalApprovedCards,
            'totalRejectedCards' => $totalRejectedCards,
            'request' => $request,
        ]);
    }

    public function show($id)
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp&nbsp' . __('Topup account'),
                'url' => route('user.top-up.create'),
            ]
        ];
        $creditCard = CreditCard::with(['user' => [
            'country',
            'state',
            'city',
        ], 'card_order', 'virtual_card:id,card_variant,increase_limit_fee'])->findOrFail($id);
        $cardholder_data = get_option('cardholder_data');
        $gateways = Gateway::query()->where('status', 1)->get();

        $cardHelper = new CardHelper();
        $cardDetails = $cardHelper->cardDetails($creditCard->card);
        $cardTransactions = $cardHelper->cardTransactions($creditCard->card);
        $spandTransactions = $cardHelper->spandTransactions($creditCard->card);

        $othercity = OtherCity::where('user_id', $creditCard->user_id)->first();
        $otherstate = OtherState::where('user_id', $creditCard->user_id)->first();
        
        $stripeCurrency = 'usd';
        return Inertia::render('Admin/CreditCard/Show', [
            'creditCard' => $creditCard,
            'cardholder_data' => $cardholder_data,
            'buttons' => $buttons,
            'gateways' => $gateways,
            'stripe_currency' => $stripeCurrency,
            'card_details' => $cardDetails,
            'transactions' => $cardTransactions,          
            'other_city' => $othercity,
            'other_state' => $otherstate,
        ]);
    }

    public function transactions()
    {
        $cardHelper = new CardHelper();
        $cardTransactions = $cardHelper->allAdminCardsTransactions();
        
        return Inertia::render('Admin/Transaction/Index', [
            'transactions' => $cardTransactions,
        ]);
    }

    public function authorizations(StripeHelper $stripeHelper)
    {
        $stripeData = $stripeHelper->authorizations('admin.credit-card.authorizations');
        $stripeCurrency = env('STRIPE_CURRENCY', 'usd');
        return Inertia::render('Admin/Authorization/Index', [
            'authorizations' => $stripeData['authorizations'] ?? [],
            'has_more' => $stripeData['has_more'] ?? [],
            'next' => $stripeData['next'] ?? '',
            'prev' => $stripeData['prev'] ?? '',
            'stripe_currency' => $stripeCurrency
        ]);
    }

    public function updateStatus($id, $status)
    {
        if($status == 'active'){
            $updateStatus = 'inactive';
        }else{
            $updateStatus = 'active';
        }

        $creditCard = CreditCard::findOrFail($id);

        $cardHelper = new CardHelper();
        $cardUpdate = $cardHelper->cardStatusUpdate($creditCard->card, $updateStatus);

        $creditCard->update([
            'status' => $updateStatus,
        ]);

        return back()->with('warning', 'Updated Successfully');
    }

    public function updateSpending($id, StripeHelper $stripeHelper)
    {
        $creditCard = CreditCard::findOrFail($id);
        $card = $stripeHelper->updateSpendingLimits($creditCard->card, request('amount'));
        $creditCard->update([
            'spending_limits' => $card['spending_limits'] / 100,
        ]);
        Notification::create([
            'user_id' => auth()->id(),
            'title' => __('Prepaid Card Spending Limits Updated'),
            'comment' => __('Prepaid Card spending limits updated to'),
            'url' => route('user.credit-cards.show', $creditCard->id),
            'for_admin' => 0,
        ]);
        return back()->with('warning', 'Updated Successfully');
    }

    public function terminate($id)
    {
        $creditCard = CreditCard::findOrFail($id);

        $cardHelper = new CardHelper();
        $cardUpdate = $cardHelper->cardTerminate($creditCard->card);

        $creditCard->update([
            'status' => 'terminate',
        ]);

        return back()->with('warning', 'Updated Successfully');
    }

    public function topups(Request $request)
    {
        $segments = request()->segments();
        $topUps = CardTopup::query()
            ->filterOn(['invoice_no', 'status'])
            ->filterOnRelation(['user_email'])
            ->with('user', 'gateway')
            ->latest()->paginate(20);

        $totalTopUps = CardTopup::count();
        $totalAmount = CardTopup::sum('amount');
        $totalApprovedTopUps = CardTopup::where('status', 'approved')->count();
        $totalRejectedTopUps = CardTopup::where('status', 'rejected')->count();
        $type = $request->type ?? 'email';

        return Inertia::render('Admin/CreditCard/Topup', [
            'segments' => $segments,
            'topUps' => $topUps,
            'request' => $request,
            'totalTopUps' => $totalTopUps,
            'totalAmount' => $totalAmount,
            'totalApprovedTopUps' => $totalApprovedTopUps,
            'totalRejectedTopUps' => $totalRejectedTopUps,
            'type' => $type,
        ]);
    }

    public function topupdetails($id)
    {
        $order = CardTopup::with('user', 'gateway')->findOrFail($id);
        $invoice_data = get_option('invoice_data');

        $meta = $order->meta ?? '';
        return Inertia::render('Admin/CreditCard/Topupdetails', [
            'order' => $order,
            'invoice_data' => $invoice_data,
            'meta' => $meta,
        ]);
    }

    public function balance($id)
    {
        $creditCard = CreditCard::findOrFail($id);

        $cardHelper = new CardHelper();
        $spandTransactions = $cardHelper->cardTransactions($creditCard->card);
        $cardDetails = $cardHelper->cardDetails($creditCard->card);
        
        $totalSpend = 0;
        foreach($spandTransactions['data'] as $spandTransaction){
            if($spandTransaction['state'] == 'CLEARED'){
                $totalSpend += $spandTransaction['amount'];
            }
        }   
        
        $available_balance = $cardDetails['spending_restrictions']['amount'] - $totalSpend;

        CreditCard::where('id', $id)->update(['available_limits' => number_format($available_balance, 2, '.', '')]);
        
        return response()->json(['updated_balance' => number_format($available_balance, 2, '.', '')]);
    }

    public function updateCard(Request $request)
    {
        $card_id = $request->card_id;
        $name_on_card = $request->name_on_card;

        $card = CreditCard::findOrFail($card_id);

        $cardHelper = new CardHelper();
        $cardDetails = $cardHelper->cardUpdate($card->card, $name_on_card);
        
        CreditCard::where('id', $card_id)->update(['name_on_card' => $name_on_card]);

        return back()->with('success', 'Card updated successfully');
    }
}
