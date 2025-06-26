<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\VirtualCurrency;
use App\Models\Withdraw;
use Auth;
use DB;

class WithdrawController extends Controller
{
    public function index(Request $request)
    {
        $withdraws = Withdraw::query()
        ->filterOn(['id', 'status'])
        ->filterOnRelation(['receiver_email'])
        ->with('receiver:id,email')
        ->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('receiver_id', auth()->id());
        })
        ->latest()
        ->paginate();

        $withdrawAmounts = Withdraw::select('virtual_currency_id', 'user_id', DB::raw('SUM(amount) as total_amount'))
            ->with('currency:id,currency,preview')
            ->where('user_id', auth()->id())
            ->groupBy('virtual_currency_id')
            ->get();

        $withdrawAmountsSent = Withdraw::select('virtual_currency_id', 'receiver_id', DB::raw('SUM(amount) as total_amount'))
            ->with('currency:id,currency,preview')
            ->where('receiver_id', auth()->id())
            ->groupBy('virtual_currency_id')
            ->get();
        return Inertia::render('User/Withdraw/Index', [
            'withdraws' => $withdraws,
            'request' => $request,
            'withdrawAmounts' => $withdrawAmounts,
            'withdrawAmountsSent' => $withdrawAmountsSent
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        return Inertia::render('User/Withdraw/Create', [
            'accounts' => $user->getCurrencyWallets(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_email' => 'required|email',
            'amount' => 'required|numeric|between:0,99999999.999999999999',
        ]);

        $receiver = User::query()->where('status', 1)
            ->where('email', $request->receiver_email);

        if (!$receiver->exists()) {
            return redirect()->back()->with('danger', "Recipient's account not found");
        }
        $receiver = $receiver->first();
        $user = Auth::user();
        $wallet = Wallet::query()
            ->where('virtual_currency_id', $request->currency_id)
            ->where('user_id', auth()->id())
            ->first();
        $wallet = Wallet::query()
            ->where('virtual_currency_id', $request->currency_id)
            ->where('user_id', auth()->id())
            ->first();
        $currency = VirtualCurrency::query()
            ->where('id', $request->currency_id)
            ->where('status', 'active')
            ->firstOrFail();
        $amount = $request->amount;
        if ($wallet->balance < $amount) {
            return redirect()->back()->with('danger', 'Insufficient balance');
        }


        DB::beginTransaction();
        try {

            $wallet->decrement('balance', $amount);
            $service_fee = $amount * $user->plan_data['service_fee']['value'] / 100;
            $after_service_fee =  $amount - $service_fee;

            Wallet::updateOrCreate(
                ['virtual_currency_id' => $request->currency_id, 'user_id' => $receiver->id],
                ['balance' => DB::raw("balance + $after_service_fee")]
            );
            Withdraw::create([
                'user_id' => auth()->id(),
                'receiver_id' => $receiver->id,
                'receiver_email' => $receiver->email,
                'amount' => $amount,
                'virtual_currency_id' => $request->currency_id,
                'currency' => $currency->currency,
                'service_fee' => $service_fee,
            ]);

            $message = "New P2P Successful";

            Notification::sendToAdmin(__('Withdraw Request'), $message, route('admin.withdraw.index'));

            Notification::create([
                'user_id' => $receiver->id,
                'title' => 'Payment Received',
                'comment' => Auth::user()->name . ' Has sent a transfer',
                'url' => url('/user/transfer/logs'),
                'for_admin' => 0,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return $e;
        }


        return redirect(route('user.withdraw.index'))->with('success', 'A new transfer successfull');
    }
}
