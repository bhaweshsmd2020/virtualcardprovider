<?php

namespace App\Http\Controllers\User;

use App\Helpers\Toastr;
use App\Models\Gateway;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Models\Deposit;
use App\Traits\Uploader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class DepositController extends Controller
{
    use Uploader;
    public function index(Request $request)
    {
        $segments = request()->segments();

        $deposits = Deposit::query()
            ->filterOn(['invoice_no', 'status', 'amount'])
            ->where('user_id', auth()->id())
            ->with('user', 'gateway')
            ->latest()->paginate(20);

        $totalDeposits = Deposit::where('user_id', auth()->id())->count();
        $totalAmount = Deposit::where('user_id', auth()->id())->sum('amount');
        $totalApprovedDeposits = Deposit::where('user_id', auth()->id())->where('status', 'approved')->count();
        $totalRejectedDeposits = Deposit::where('user_id', auth()->id())->where('status', 'rejected')->count();
        $type = $request->type ?? 'email';

        return Inertia::render('User/Deposit/Index', [
            'segments' => $segments,
            'deposits' => $deposits,
            'request' => $request,
            'totalDeposits' => $totalDeposits,
            'totalAmount' => $totalAmount,
            'totalApprovedDeposits' => $totalApprovedDeposits,
            'totalRejectedDeposits' => $totalRejectedDeposits,
            'type' => $type,
        ]);
    }
    public function show($id)
    {
        $order = Deposit::with('user', 'gateway')->findOrFail($id);
        $invoice_data = get_option('invoice_data');

        $creditCard = CreditCard::with([
            'user' => [
                'country',
                'state',
                'city',
            ],
            'card:id,card_variant'
        ])->where('id', $order->credit_card_id)
            ->firstOrFail();
        return Inertia::render('User/Deposit/Show', [
            'order' => $order,
            'invoice_data' => $invoice_data,
            'meta' => $order->meta,
            'creditCard' => $creditCard,
        ]);
    }
    public function store(Request $request)
    {
        $gateway = Gateway::where('status', 1)->findOrFail($request->gateway_id);
        $total = $request->total;

        $user = Auth::user();
        $deposit_fee = $total * $user->plan_data['deposit_fee']['value'] / 100;
        $with_deposit_fee = $total + $deposit_fee ?? 0;

        $subTotal = ($with_deposit_fee * $gateway->multiply) + $gateway->charge;
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
        Session::put('credit_card_id', $request->credit_card_id);
        Session::put('deposit_fee', $deposit_fee ?? 0);
        Session::put('requested_amount', $request->total);

        $payment_data['currency'] = $gateway->currency ?? 'USD';
        $payment_data['email'] = auth()->user()->email;
        $payment_data['name'] = auth()->user()->name;
        $payment_data['phone'] = $request->phone;
        $payment_data['billName'] = 'Deposit';
        $payment_data['amount'] = $with_deposit_fee;
        $payment_data['test_mode'] = $gateway->test_mode;
        $payment_data['charge'] = $gateway->charge ?? 0;
        $payment_data['pay_amount'] = number_format($subTotal, 0, '.', '');
        $payment_data['gateway_id'] = $gateway->id;

        $callback['success'] = route('user.deposits.status.update', 'success');
        $callback['fail'] = route('user.deposits.status.update', 'failed');
        $callback['coingate_webhook_route'] = 'coingate.card-deposit.callback';

        Session::put('call_back', $callback);

        if (!empty($gateway->data)) {
            foreach (json_decode($gateway->data ?? '') ?? [] as $key => $info) {
                $payment_data[$key] = $info;
            };
        }

        return $gateway->namespace::make_payment($payment_data);
    }

    public function wallet(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        if ($request->total > $user->balance) {
            return back()->with('danger', __('Insufficient balance'));
        }
        $total = $request->total;

        Session::put('credit_card_id', $request->credit_card_id);
        Session::put('is_wallet', true);
        Session::put('deposit_fee', $user->plan_data['deposit_fee']['value'] ?? 0);
        $payment_data['currency'] = 'USD';
        $payment_data['email'] = $user->email;
        $payment_data['name'] = $user->name;
        $payment_data['billName'] = 'Deposit';
        $payment_data['amount'] = $total;
        $payment_data['status'] = 1;
        $payment_data['pay_amount'] = number_format($request->total, 0, '.', '');
        $payment_data['gateway_id'] = null;

        $callback['success'] = route('user.deposits.status.update', 'success');
        $callback['fail'] = route('user.deposits.status.update', 'failed');
        Session::put('payment_info', $payment_data);

        Session::put('call_back', $callback);

        return $this->success();
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
        $is_wallet = Session::get('is_wallet');
        Session::forget('payment_info');
        Session::forget('call_back');
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        DB::transaction(function () use ($paymentInfo, $user, $is_wallet) {
            if (!$is_wallet) {
                $status = $paymentInfo['payment_method'] == 'manual' ? 'pending' : $paymentInfo['status'] ?? 'pending';
            }
            $tax = get_option('tax');
            if ($is_wallet) {
                $status = 'approved';
                $user->balance -= $paymentInfo['amount'];
                $user->save();
            }
            $deposit = new Deposit();
            $deposit->user_id = auth()->id();
            $deposit->payment_id = $is_wallet ? null : $paymentInfo['payment_id'];
            $deposit->gateway_id = $is_wallet ? null : $paymentInfo['gateway_id'];
            $deposit->credit_card_id = Session::get('credit_card_id');
            $deposit->amount = $paymentInfo['amount'];
            $deposit->order_uid = session('order_uid', null);
            $deposit->tax = $tax;
            $deposit->deposit_fee = Session::get('deposit_fee');
            $deposit->status = $status ?? 'pending';
            $deposit->requested_amount = Session::get('requested_amount');

            if (isset($paymentInfo['meta'])) {
                $deposit->meta = $paymentInfo['meta'];
            }
            $deposit->save();

            Notification::create([
                'user_id' => auth()->id(),
                'title' => __('New Deposit'),
                'comment' => __('An User has New Deposit'),
                'url' => route('admin.deposits.show', $deposit->id),
            ]);
        });

        $message = $paymentInfo['status'] == 1 ? __('Your Deposit is complete') : __('Your Deposit is complete admin will review this payment manually for approval.');

        Toastr::success($message);

        return to_route('user.deposits.index')->with('success', $message);
    }

    private function failed()
    {
        Session::forget('payment_info');
        Session::forget('call_back');
        Session::flash('error', true);
        Session::forget('credit_card_id');
        $message = __('Your Deposit request is failed');

        Toastr::danger($message);
        return to_route('user.deposits.index');
    }
}
