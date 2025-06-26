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
use App\Models\User;
use Illuminate\Support\Facades\Session;

class QrTopUpController extends Controller
{
    public function index($uuid)
    {
        $user = User::query()
            ->select('uuid', 'last_name', 'first_name', 'email', 'avatar', 'phone')
            ->where('uuid', $uuid)->firstOrFail();
        $gateways = Gateway::query()
            ->where('is_auto', 1)
            ->where('status', 1)->get();
        $invoice_data = get_option('invoice_data');
        return Inertia::render('User/QrPayment/Index', [
            'gateways' => $gateways,
            'invoice_data' => $invoice_data,
            'user' => $user,
        ]);
    }
    public function invoice($invoice)
    {
        $topUp = TopUp::query()
            ->with(['user:id,uuid,last_name,first_name,avatar', 'gateway'])
            ->where('payment_id', $invoice)->firstOrFail();
        $invoice_data = get_option('invoice_data');
        return Inertia::render('User/QrPayment/Invoice', [
            'invoice_data' => $invoice_data,
            'topUp' => $topUp
        ]);
    }


    public function store(Request $request)
    {
        $gateway = Gateway::where('status', 1)->findOrFail($request->gateway_id);
        $user = User::query()->where('uuid', $request->uuid)->firstOrFail();
        $total = $request->total;
        $subTotal = ($total * $gateway->multiply) + $gateway->charge;
        $transaction_fee = $total * $user->plan_data['transaction_fee']['value'] / 100;

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
        Session::put('rate', 1);
        Session::put('requested_amount', $total - $transaction_fee);
        $payment_data['currency'] = $gateway->currency ?? 'USD';
        $payment_data['email'] = $user->email;
        $payment_data['name'] = $user->name;
        $payment_data['phone'] = $request->phone;
        $payment_data['billName'] = 'Topup';
        $payment_data['amount'] = $total;
        $payment_data['test_mode'] = $gateway->test_mode;
        $payment_data['charge'] = $gateway->charge ?? 0;
        $payment_data['pay_amount'] = number_format($subTotal, 0, '.', '');
        $payment_data['gateway_id'] = $gateway->id;

        Session::put('senderInfo', $request->senderInfo);
        Session::put('user_uuid', $request->uuid);

        $callback['success'] = route('qr-top-up.status.update', 'success');
        $callback['fail'] = route('qr-top-up.status.update', 'failed');

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

        Session::forget('payment_info');
        Session::forget('call_back');
        /**
         * @var \App\Models\User
         */
        $user = User::query()->where('uuid', Session::get('user_uuid'))->firstOrFail();

        DB::transaction(function () use ($paymentInfo, $user) {
            $status = $paymentInfo['payment_method'] == 'manual' ? 'pending' : $paymentInfo['status'] ?? 'pending';
            $user->balance += $paymentInfo['amount'];
            $user->save();

            $topUp = new TopUp();
            $topUp->user_id = $user->id;
            $topUp->payment_id = $paymentInfo['payment_id'];
            $topUp->gateway_id = $paymentInfo['gateway_id'];
            $topUp->amount = $paymentInfo['amount'];
            $topUp->status = $status;
            $topUp->type = 'qr_top_up';
            $topUp->meta = Session::get('senderInfo');
            $topUp->order_uid = session('order_uid', null);
            $topUp->transaction_fee = Session::get('transaction_fee');
            $topUp->rate = Session::get('rate');
            $topUp->requested_amount = Session::get('requested_amount');
            $topUp->save();

            Notification::create([
                'user_id' => $user->id,
                'title' => __('New TopUp'),
                'comment' => __('A User has New Topup'),
                'url' => route('admin.top-up.show', $topUp->id),
            ]);
        });

        $message = $paymentInfo['status'] == 1 ? __('Your Topup is complete') : __('Your Topup is complete admin will review this payment manually for approval.');

        Toastr::success($message);

        return to_route('qr-top-up.invoice', $paymentInfo['payment_id']);
    }

    private function failed()
    {
        Session::forget('payment_info');
        Session::forget('call_back');
        Session::forget('user_uuid');
        Session::flash('error', true);
        $message = __('Your Topup request is failed');

        Toastr::danger($message);
        return to_route('home');
    }
}
