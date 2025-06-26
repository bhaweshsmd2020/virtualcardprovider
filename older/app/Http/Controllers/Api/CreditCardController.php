<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Services\StripeHelper;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class CreditCardController extends Controller
{
    public function credit_card_current_spend($uuid, StripeHelper $stripeHelper)
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        if (!env('STRIPE_API_KEY')) {
            return 0;
        }
        if ($user->isAdmin()) {
            $creditCard = CreditCard::query()->where('uuid', $uuid)->firstOrFail();
        } else {
            $creditCard = $user->credit_cards()->where('uuid', $uuid)->firstOrFail();
        }
        if ($creditCard) {
            $totalSpend = $stripeHelper->currentSpend($creditCard->card);
        }

        return $totalSpend ?? 0;
    }

    public function createEphemeralKey(Request $request)
    {
        $stripe = new StripeClient(env('STRIPE_API_KEY'));

        try {
            $params = json_decode($request->getContent(), true);

            $ephemeralKey = $stripe->ephemeralKeys->create([
                'nonce' => $params['nonce'],
                'issuing_card' => $params['card_id'],
            ], [
                'stripe_version' => '2022-08-01'
            ]);

            return response()->json(['ephemeralKeySecret' => $ephemeralKey->secret]);
        } catch (UnexpectedValueException $e) {
            return response()->json(['error' => 'Unexpected value.'], 400);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature.'], 400);
        }
    }
}
