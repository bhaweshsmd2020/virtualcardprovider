<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Notifications;
use App\Models\CryptoTransaction;
use App\Services\CoinPaymentsAPI;

class CronController extends Controller
{
    use Notifications;


    /**
     * notify to subscribers before expire the subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function notifyToUser()
    {
        $willExpire = today()->addDays(7)->format('Y-m-d');
        $users = User::whereHas('subscription')->with('subscription')->where('will_expire', $willExpire)->latest()->get();

        foreach ($users as $key => $user) {
            $this->sentWillExpireEmail($user);
        }

        return "Cron job executed";
    }

    public function coinPaymentStatus()
    {
        $cps = new CoinPaymentsAPI();
        $public_key = '';
        $private_key = '';

        $cps->Setup($private_key, $public_key);

        $transactions = CryptoTransaction::where('status', 1)
            ->where('expire_at', '>', now())
            ->get();

        foreach ($transactions as $transaction) {
            $response = $cps->get_wdata($transaction['data']['txn_id']);
          
        }
        return "Cron job executed";
    }
}
