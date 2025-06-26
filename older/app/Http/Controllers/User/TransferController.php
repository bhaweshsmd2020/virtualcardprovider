<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\TransferTransaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transfer_details = get_option('transfer_details');
        return Inertia::render('User/Transfer/Index', [
            'accounts' => $user->getCurrencyWallets(),
            'transfer_details' => $transfer_details
        ]);
    }
    public function logs(Request $request)
    {
        $segments = request()->segments();

        $transferTransactions = TransferTransaction::query()
            ->filterOn(['id', 'from_amount', 'to_amount'])
            ->where('user_id', auth()->id())
            ->latest()->paginate(20);

        $totalTransferTransaction = TransferTransaction::query()->where('user_id', auth()->id())
            ->count();
        $totalServiceFee = TransferTransaction::query()->where('user_id', auth()->id())
            ->sum('service_fee');
        $todayTransfers = TransferTransaction::query()->where('user_id', auth()->id())
            ->whereDate('created_at', date('Y-m-d'))->count();
        $transfer_details = get_option('transfer_details');
        return Inertia::render('User/Transfer/Logs', [
            'segments' => $segments,
            'transferTransactions' => $transferTransactions,
            'request' => $request,
            'totalTransferTransaction' => $totalTransferTransaction,
            'totalServiceFee' => $totalServiceFee,
            'todayTransfers' => $todayTransfers,
            'transfer_details' => $transfer_details
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'transfer_to' => 'required|in:balance,wallet',
            'amount' => 'required|numeric|min:0'
        ]);
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        $wallet = Wallet::query()
            ->where('id', $request->wallet_id)
            ->where('user_id', $user->id)
            ->first();
        $amount = $request->amount;
        $service_fee = $amount * $user->plan_data['service_fee']['value'] / 100;
        $after_service_fee =  $amount - $service_fee;
        DB::transaction(function () use ($user, $wallet, $request, $service_fee, $after_service_fee) {
            if (!$wallet) {
                return redirect()->route('user.account')->with('error', 'Wallet not found');
            }
            if ($request->transfer_to === 'balance') {
                $default_currency = $request->transfer_currency;
                $transfer_currency =  $request->default_currency;

                if ($request->requestedAmount > $wallet->balance) {
                    return redirect()->route('user.account')->with('error', 'Insufficient balance');
                }
                $wallet->decrement('balance', $request->requestedAmount);
                $user->increment('balance', $after_service_fee);
            }
            if ($request->transfer_to === 'wallet') {
                $default_currency = $request->default_currency;
                $transfer_currency =  $request->transfer_currency;
                if ($request->requestedAmount > $user->balance) {
                    return redirect()->route('user.account')->with('error', 'Insufficient balance');
                }
                $wallet->increment('balance', $after_service_fee);
                $user->decrement('balance', $request->requestedAmount);
            }
            $user->save();

            TransferTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $request->wallet_id,
                'transfer_to' => $request->transfer_to,
                'from_amount' => $request->requestedAmount,
                'to_amount' => $after_service_fee,
                'default_currency' => $default_currency,
                'transfer_currency' => $transfer_currency,
                'service_fee' => $service_fee,
            ]);
            Notification::create([
                'user_id' => auth()->id(),
                'title' => __('New Transfer'),
                'comment' => __('A Transfer has been submitted'),
                'url' => route('admin.transfers.index'),
            ]);
        });

        return redirect()->route('user.account')->with('success', 'Transfer successfully');
    }
}
