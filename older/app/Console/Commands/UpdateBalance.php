<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CreditCard;
use Carbon\Carbon;
use App\Helpers\CardHelper;

class UpdateBalance extends Command
{
    protected $signature = 'subscription:update-balance';
    protected $description = 'Update user balance for expired or renewing subscriptions';

    public function handle()
    {
        $creditcards = CreditCard::where('status', 'active')->get();

        foreach ($creditcards as $creditcard) {
            $cardHelper = new CardHelper();
            $spandTransactions = $cardHelper->cardTransactions($creditcard->card);
            $cardDetails = $cardHelper->cardDetails($creditcard->card);
            
            $totalSpend = 0;
            foreach($spandTransactions['data'] as $spandTransaction){
                if($spandTransaction['state'] == 'CLEARED'){
                    $totalSpend += $spandTransaction['amount'];
                }
            }   
            
            $available_balance = $cardDetails['spending_restrictions']['amount'] - $totalSpend;
    
            CreditCard::where('id', $creditcard->id)->update(['available_limits' => number_format($available_balance, 2, '.', '')]);
            
        }

        $this->info('Card balances updated successfully.');
    }
}
