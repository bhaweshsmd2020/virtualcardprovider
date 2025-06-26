<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Gateway;
use App\Services\CoinPaymentsAPI;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\CardLimit;
use App\Models\CreditCard;

class CardController extends Controller
{
    public function create()
    {
        $user_id = auth()->user()->id;
        $cardlimits = CardLimit::where('user_id', $user_id)->get();

        $gateways = Gateway::query()->where('status', 1)->get();
        $cards = Card::query()
            ->where('status', 'active')
            ->with('category:id,title,icon')
            ->get();

        $cards = $cards->map(function ($card) use ($cardlimits) {
            $cardLimit = $cardlimits->firstWhere('card_type', $card->id);
            $card->card_limit = $cardLimit ? $cardLimit->limit : $card->card_limit;            
            return $card;
        });

        $creditcards = CreditCard::where('user_id', $user_id)->where('status', 'active')->get();

        return Inertia::render('User/Card/Create', [
            'gateways' => $gateways,
            'cards' => $cards,
            'creditcards' => $creditcards,
        ]);
    }
}
