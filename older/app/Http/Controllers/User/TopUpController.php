<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Helpers\Toastr;
use App\Models\Gateway;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TopUp;
use App\Models\VirtualCurrency;
use App\Traits\Uploader;
use Illuminate\Support\Facades\Session;
use Auth;
use App\Models\Wallet;
use Illuminate\Support\Str;
use App\Models\TopupReceipt;

class TopUpController extends Controller
{
    use Uploader;
    public function index(Request $request)
    {
        $segments = request()->segments();
        $topUps = TopUp::query()
            ->filterOn(['invoice_no', 'status', 'amount'])
            ->where('user_id', auth()->id())
            ->with('user', 'gateway')
            ->latest()->paginate(20);

        $totalTopUps = TopUp::where('user_id', auth()->id())->count();
        $totalAmount = TopUp::where('user_id', auth()->id())->sum('amount');
        $totalApprovedTopUps = TopUp::where('user_id', auth()->id())->where('status', 'approved')->count();
        $totalRejectedTopUps = TopUp::where('user_id', auth()->id())->where('status', 'rejected')->count();
        $type = $request->type ?? 'email';

        return Inertia::render('User/TopUp/Index', [
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
    public function show($id)
    {
        $order  = TopUp::with('user', 'gateway')->findOrFail($id);
        $invoice_data = get_option('invoice_data');

        $receipt = TopupReceipt::where('order_id', $order->id)->first();
        if(!empty($receipt)){
            $receiptDetail = $receipt;
        }else{
            $receiptDetail = null;
        }

        $meta = $order->meta ?? '';
        return Inertia::render('User/TopUp/Show', [
            'order' => $order,
            'invoice_data' => $invoice_data,
            'meta' => $meta,
            'receiptDetail' => $receiptDetail,
        ]);
    }
    public function create()
    {
        $gateways = Gateway::query()->where('status', 1)->get();
        $bank_gateway = Gateway::query()->where('id', 14)->first();

        return Inertia::render('User/TopUp/Balance', [
            'gateways' => $gateways,
            'bank_gateway' => $bank_gateway,
        ]);
    }

    public function createWallet()
    {
        $gateways = Gateway::query()->where('status', 1)->get();
        $user = Auth::user();
        return Inertia::render('User/TopUp/Wallet', [
            'gateways' => $gateways,
            'accounts' => $user->getCurrencyWallets(),
        ]);
    }

    public function store(Request $request)
    {
        $pay_type  = $request->pay_type;

        if($pay_type == 'bank'){          
            $gateway_id = 14;
        }else{
            $gateway_id = $request->gateway_id;
        }

        $gateway  = Gateway::findOrFail($request->gateway_id);
        $total  = $request->total;
        $user = Auth::user();
        $transaction_fee = $total * $user->plan_data['transaction_fee']['value'] / 100;
        $with_transaction_fee = $total + $transaction_fee ?? 0;
        $subTotal  = ($with_transaction_fee * $gateway->multiply) + $gateway->charge;

        if($pay_type == 'bank'){ 
            $topUp = new TopUp();
            $topUp->user_id             = auth()->id();
            $topUp->payment_id          = 'pi_' . Str::random(8) . strtoupper(Str::random(16));
            $topUp->gateway_id          = $gateway_id;
            $topUp->wallet_id           = null;
            $topUp->amount              = $with_transaction_fee;
            $topUp->status              = 'pending';
            $topUp->type                = 'balance';
            $topUp->order_uid           = null;
            $topUp->transaction_fee     = $transaction_fee ?? 0;
            $topUp->rate                = $request?->rate ?? 1;
            $topUp->requested_amount    = $request->requested_amount;
            $topUp->meta                = null;
            $topUp->pay_type            = $pay_type;
            $topUp->save();
            
            $newTopUpId = $topUp->id;

            $receipt = new TopupReceipt();
            $receipt->order_id = $newTopUpId;
            $receipt->image = $this->saveFile($request, 'image');
            $receipt->comments = $request->comment;
            $receipt->save(); 

            Notification::create([
                'user_id' => auth()->id(),
                'title' => __('New TopUp'),
                'comment' => __('A User has New Topup Request'),
                'url' => route('admin.top-up.show', $newTopUpId),
            ]);

            $message = 'Your account topup is complete admin will review this payment manually for approval.';
            $redirectUrl = '/user/account';
            $buttonmes = 'Check Account Balance';

            return redirect('/user/top-up?success=' . urlencode($message) . '&redirect=' . urlencode($redirectUrl) . '&buttonmes=' . urlencode($buttonmes)); 
        }

        if ($gateway->min_amount > $subTotal) {
            return redirect()->back()->with('danger', __('The minimum transaction amount is ' . $gateway->min_amount));
        }
        if ($gateway->max_amount != -1) {
            if ($gateway->max_amount < $subTotal) {
                return redirect()->back()->with('danger', __('The maximum transaction amount is ' . $gateway->max_amount));
            }
        }

        if ($gateway->is_auto == 0) {
            $request->validate([
                'manualPayment.comment' => ['required', 'string', 'max:500'],
                'manualPayment.image' => ['required', 'image', 'max:2048'], // 2MB
            ]);

            $payment_data['comment'] = $request->input('manualPayment.comment');
            $image = $this->saveFile($request, 'manualPayment.image');
            $payment_data['screenshot'] = $image;
            $payment_data['comment'] = $request->input('manualPayment.comment');
        }
        Session::put('transaction_fee', $transaction_fee ?? 0);
        Session::put('wallet_id', $request->wallet_id);
        Session::put('rate', $request?->rate ?? 1);
        Session::put('requested_amount', $request->requested_amount);

        $payment_data['currency']   = $gateway->currency ?? 'USD';
        $payment_data['email']      = auth()->user()->email;
        $payment_data['name']       = auth()->user()->name;
        $payment_data['phone']      = $request->phone;
        $payment_data['billName']   = 'Topup';
        $payment_data['amount']     = $with_transaction_fee;
        $payment_data['test_mode']  = $gateway->test_mode;
        $payment_data['charge']     = $gateway->charge ?? 0;
        $payment_data['pay_amount'] =  number_format($subTotal, 0, '.', '');
        $payment_data['gateway_id']  = $gateway->id;

        $callback['success'] = route('user.top-up.status.update', 'success');
        $callback['fail'] = route('user.top-up.status.update', 'failed');
        $callback['coingate_webhook_route'] = 'coingate.top-up.callback';

        Session::put('call_back', $callback);

        if (!empty($gateway->data)) {
            foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
        }

        return $gateway->namespace::make_payment($payment_data);
    }

    public function update($status)
    {

        abort_if(!Session::has('call_back'), 404);

        return $status == 'success' ? $this->success() : $this->failed();
    }

    private function success()
    {
        abort_if(!Session::has('payment_info'), 404);

        $paymentInfo = Session::get('payment_info');
        $wallet_id = Session::get('wallet_id');
        Session::forget('payment_info');
        Session::forget('call_back');

        DB::transaction(function () use ($paymentInfo, $wallet_id) {
            $status = $paymentInfo['payment_method'] == 'manual' ? 'pending' : $paymentInfo['status'] ?? 'pending';
            $topUp = new TopUp();
            $topUp->user_id             = auth()->id();
            $topUp->payment_id          = $paymentInfo['payment_id'];
            $topUp->gateway_id          = $paymentInfo['gateway_id'];
            $topUp->wallet_id           = $wallet_id ?? null;
            $topUp->amount              = $paymentInfo['amount'];
            $topUp->status              = $status;
            $topUp->type                = $wallet_id ? 'wallet' : 'balance';
            $topUp->order_uid           = session('order_uid', null);
            $topUp->transaction_fee     =  Session::get('transaction_fee');
            $topUp->rate                =  Session::get('rate');
            $topUp->requested_amount    =  Session::get('requested_amount');
            if (isset($paymentInfo['meta'])) {
                $topUp->meta    = $paymentInfo['meta'];
            }            
            $topUp->pay_type            = 'card';
            $topUp->save();
            
            $newTopUpId = $topUp->id;

            $topupData = TopUp::with('user', 'gateway')->findOrFail($newTopUpId);
            $user = $topupData->user;

            if ($topupData->wallet_id) {
                $wallet = Wallet::query()
                    ->with('currency')
                    ->where('id', $topupData->wallet_id)
                    ->where('user_id', $user->id)
                    ->first();
                $defaultCurrency = VirtualCurrency::query()
                    ->with('rates.exchange_currency')
                    ->where('is_default', 1)
                    ->firstOrFail();
                if ($wallet->currency->currency == $defaultCurrency->currency) {
                    $amount = $topupData->requested_amount;
                } else {
                    $amount = $topupData->requested_amount;
                }
                ;

                $wallet->balance += $amount;
                $wallet->save();
            } else {
                $user->balance += $topupData->requested_amount;
                $user->save();
            }
            $topupData->update(['status' => 'approved']);

            Notification::create([
                'user_id' => auth()->id(),
                'title' => __('New TopUp'),
                'comment' => __('A User has New Topup'),
                'url' => route('admin.top-up.show', $topupData->id),
            ]);
        });

        if(!empty($wallet_id)){
            $message = $paymentInfo['status'] == 1 ? __('Your wallet has been topup successful') : __('Your wallet topup is complete admin will review this payment manually for approval.');
            $redirectUrl = '/user/wallet';
            $buttonmes = 'Check Wallet Balance';
        }else{
            $message = $paymentInfo['status'] == 1 ? __('Your account has been topup successful') : __('Your account topup is complete admin will review this payment manually for approval.');
            $redirectUrl = '/user/account';
            $buttonmes = 'Check Account Balance';
        }        

        return redirect('/user/top-up?success=' . urlencode($message) . '&redirect=' . urlencode($redirectUrl) . '&buttonmes=' . urlencode($buttonmes));
    }

    private function failed()
    {
        Session::forget('payment_info');
        Session::forget('call_back');
        Session::flash('error', true);
        $message = __('Your topup request is failed');

        return redirect('/user/top-up?error=' . urlencode($message));
    }
}
