<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExchangeTransaction;
use App\Models\Notification;
use App\Models\VirtualCurrency;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Auth;

class ExchangeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $exchange_details = get_option('exchange_details');
        return Inertia::render('User/Exchange/Index', [
            'accounts' => $user->getCurrencyWallets(),
            'request' => $request,
            'exchange_details' => $exchange_details
        ]);
    }
    public function logs(Request $request)
    {
        $segments = request()->segments();

        $exchangeTransactions = ExchangeTransaction::query()
            ->filterOn(['id', 'from_amount', 'to_amount'])
            ->where('user_id', auth()->id())
            ->latest()->paginate(20);

        $exchange_details = get_option('exchange_details');
        $totalServiceFee = ExchangeTransaction::query()->where('user_id', auth()->id())
            ->sum('service_fee');
        $total = ExchangeTransaction::query()->where('user_id', auth()->id())
            ->count();
        $todayExchanges = ExchangeTransaction::query()->where('user_id', auth()->id())
            ->whereDate('created_at', date('Y-m-d'))->count();

        return Inertia::render('User/Exchange/Logs', [
            'segments' => $segments,
            'exchangeTransactions' => $exchangeTransactions,
            'request' => $request,
            'exchange_details' => $exchange_details,
            'totalServiceFee' => $totalServiceFee,
            'total' => $total,
            'todayExchanges' => $todayExchanges,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from' => 'required|exists:virtual_currencies,id',
            'to' => 'required|exists:virtual_currencies,id',
            'amount' => 'required|numeric|between:0,99999999.999999999999'
        ]);
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $currencyFrom = VirtualCurrency::query()->with('rates')
            ->findOrFail($request->from);
        $currencyTo = VirtualCurrency::query()->findOrFail($request->to);
        $amount = $request->amount;
        $findRate = collect($currencyFrom->rates)
            ->where('virtual_currency_exchange_id', $currencyTo->id)->first();
        $transferAmount = (float) $amount * (float) $findRate->rate;
        $service_fee = $transferAmount * $user->plan_data['service_fee']['value'] / 100;
        $after_service_fee = $transferAmount - $service_fee;

        $walletFrom = Wallet::query()->where('user_id', auth()->id())
            ->where('virtual_currency_id', $request->from)->firstOrFail();
        if ($walletFrom->balance < $amount) {
            return back()->with('danger', 'Insufficient balance');
        }
        $walletTo = Wallet::query()->where('user_id', auth()->id())
            ->where('virtual_currency_id', $request->to)->firstOrFail();

        $walletFrom->decrement('balance', $amount);
        $walletTo->increment('balance', $after_service_fee);

        $message = "A user has exchanged currency from " . $currencyFrom->currency .  ' to ' . $currencyTo->currency . "The amount transferred is" . $amount . ' ' . $currencyFrom->currency;
        Notification::sendToAdmin(__('Exchange performed'), $message, route('admin.exchanges.index'));

        ExchangeTransaction::create([
            'user_id' => auth()->id(),
            'wallet_id' => $walletFrom->id,
            'to_wallet_id' => $walletTo->id,
            'from_amount' => $amount,
            'to_amount' => $transferAmount,
            'default_currency' => $currencyFrom->currency,
            'transfer_currency' => $currencyTo->currency,
            'service_fee' => $service_fee,
        ]);

        return back()->with('success', 'Exchange successful');
    }
}
