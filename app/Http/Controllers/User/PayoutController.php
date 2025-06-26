<?php

namespace App\Http\Controllers\User;

use App\Mail\OtpMail;
use App\Models\Payout;
use App\Mail\PayoutMail;
use App\Models\PayoutMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\PageHeader;
use App\Models\VirtualCurrency;
use App\Models\Wallet;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Session;
use Auth;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;

class PayoutController extends Controller
{
    public function index()
    {
        PageHeader::set()->title('Payout methods')
            ->buttons([
                ['name' => 'Payout History', 'url' => route('user.payout.logs')]
            ]);

        $pending_amount = Payout::where('user_id', Auth::id())->whereStatus('pending')->sum('amount');
        $approved_amount = Payout::where('user_id', Auth::id())->whereStatus('approved')->sum('amount');

        $methods = PayoutMethod::whereStatus(1)->latest()->get();
        return Inertia::render('User/Payout/Index', [
            'methods' => $methods,
            'pending_amount' => amount_format($pending_amount, 'icon'),
            'approved_amount' => amount_format($approved_amount, 'icon'),
        ]);
    }


    public function show($method_id)
    {
        PageHeader::set()->title('Payout History')
            ->buttons([
                ['name' => 'Back', 'url' => route('user.payout.index')]
            ]);

        $method = PayoutMethod::whereStatus(1)->findOrFail($method_id);

        $fields = json_decode($method->data);
        $user = Auth::user();
        return Inertia::render('User/Payout/Show', [
            'method' => $method,
            'fields' => $fields,
            'accounts' => $user->getCurrencyWallets()
        ]);
    }


    public function sentOtp(Request $request, $methodId)
    {
        $request->validate([
            'amount' => 'required|integer',
        ]);
        $user = Auth::user();
        $payoutMethod = PayoutMethod::whereStatus(1)->findOrFail($methodId);

        if (!$payoutMethod) {
            return response()->json('Method not found.', 404);
        }

        $walletFrom = null;
        $currencyFrom = null;
        $amount = null;

        if ($request->payoutWith == 'wallet') {
            $currencyFrom = VirtualCurrency::query()->findOrFail($request->currency_id);
            $walletFrom = Wallet::query()->where('user_id', auth()->id())
                ->where('virtual_currency_id', $request->currency_id)
                ->firstOrFail();

            if (isset($payoutMethod->rates[$currencyFrom->currency])) {
                $amount = (float)$request->amount * (float)$payoutMethod->rates[$currencyFrom->currency];
            } else {
                return back()->withErrors(['currency' => 'Invalid Currency'])->onlyInput('currency');
            }
        }

        if ($request->payoutWith == 'balance') {
            $defaultCurrency = VirtualCurrency::query()->where('is_default', 1)->firstOrFail();
            $amount = (float)$request->amount * (float)$payoutMethod->rates[$defaultCurrency->currency];
        }

        if ($walletFrom && $walletFrom->balance < $request->amount && $request->payoutWith == 'wallet') {
            return back()->withErrors(['amount' => 'Insufficient Balance'])->onlyInput('amount');
        } elseif ($user->balance < $request->amount && $request->payoutWith == 'balance') {

            return back()->withErrors(['amount' => 'Insufficient Balance'])->onlyInput('amount');
        }

        if ($payoutMethod->min_limit > $amount) {
            return back()->withErrors(['amount' => 'Minimum transaction amount ' . $payoutMethod->min_limit])->onlyInput('amount');
        } elseif ($payoutMethod->max_limit < $amount) {
            return back()->withErrors(['amount' => 'Maximum transaction amount ' . $payoutMethod->max_limit])->onlyInput('amount');
        }

        $otp = rand();
        $payoutMetaData = [
            'payout_otp' => $otp,
            'payout_amount' => $amount,
            'method_id' => $payoutMethod->id,
            'payout_infos' => $request->inputs,
            'currency_id' => $request?->currency_id,
            'currency' => $currencyFrom?->currency,
            'payout_with' => $request->payoutWith,
            'requested_amount' => $request->amount,
        ];

        session()->put('payoutdata', $payoutMetaData);

        try {

            $template = EmailTemplate::where(['id' => '10', 'type' => 'email'])->select('subject', 'body')->first();

            $template_sub = $template->subject;
            $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);  
            $template_msg = str_replace('{otp}', $otp, $template_msg); 

            $emailHelper = new EmailHelper();
            $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

        } catch (Exception $e) {
            return response()->json('The Mail Settings Has Issues', 404);
        }

        return Inertia::location(route('user.payout.confirmation'));
    }


    function confirmation()
    {
        
        abort_if(!Session::has('payoutdata'), 404);
        $segments = request()->segments();
        $sessionData = Session::get('payoutdata');
        $method_id = $sessionData['method_id'];
        $payout_otp = $sessionData['payout_otp'];
        $payout_amount = $sessionData['payout_amount'];

        $method = PayoutMethod::whereStatus(1)->findOrFail($method_id);

        $userPayoutInfo = $sessionData['payout_infos'] ?? '';

        return Inertia::render('User/Payout/Confirmation', [
            'method' => $method,
            'userPayoutInfo' => $userPayoutInfo,
            'payout_otp' => $payout_otp,
            'payout_amount' => $payout_amount,
            'segments' => $segments,
        ]);
    }

    public function makeRequest(Request $request)
    {
        abort_if(!Session::get('payoutdata'), 404);

        $sessionData = Session::get('payoutdata');
        $method_id = $sessionData['method_id'];
        $payout_otp = $sessionData['payout_otp'];
        $payout_amount = $sessionData['payout_amount'];
        $currency_id = $sessionData['currency_id'];
        $requested_amount = $sessionData['requested_amount'];
        $payout_with = $sessionData['payout_with'];
        $request->validate([
            'otp' => 'required|integer',
        ]);
        $user = Auth::user();
        if ($payout_otp != $request->otp) {
            return back()->withErrors([
                'otp' => 'OTP Doesnt Match',
            ])->onlyInput('otp');
        }

        $method = PayoutMethod::whereStatus(1)->findOrFail($method_id);
        $defaultCurrency = VirtualCurrency::query()->where('is_default', 1)->firstOrFail();
        if ($payout_with == 'wallet') {
            $wallet = Wallet::query()
                ->with('currency')
                ->where('virtual_currency_id', $currency_id)
                ->firstOrFail();
            if ($requested_amount > $wallet->balance) {
                return back()->withErrors([
                    'otp' => 'Balance Low',
                ])->onlyInput('otp');
            }
        }
        if ($payout_with == 'balance') {
            if ($requested_amount > $user->balance) {
                return back()->withErrors([
                    'otp' => 'Balance Low',
                ])->onlyInput('otp');
            }
        }


        $userPayoutInfo = $sessionData['payout_infos'] ?? '';

        $charge = $method->charge_type == 'fixed' ? $method->fixed_charge : ($method->percent_charge / 100) * $payout_amount;
        $payout_after_charge = $payout_amount - $charge;

        DB::beginTransaction();
        try {

            $payout = Payout::create([
                'charge' => $charge,
                'user_id' => $user->id,
                'wallet_id' => $payout_with == 'wallet' ? $wallet->id : null,
                'amount' => $payout_amount,
                'currency' => $method->currency_name,
                'requested_currency' => $payout_with == 'wallet' ? $wallet->currency->currency : $defaultCurrency->currency,
                'requested_amount' => $requested_amount,
                'payout_method_id' => $method_id,
                'meta' => $userPayoutInfo
            ]);
            if ($payout_with == 'wallet') {
                $walletFrom = Wallet::query()->where('user_id', $user->id)
                    ->where('virtual_currency_id', $currency_id)->firstOrFail();
                $walletFrom->decrement('balance', $requested_amount);
            }
            if ($payout_with == 'balance') {
                $user->balance -= $requested_amount;
                $user->save();
            }
            Mail::to(env('MAIL_TO'))->queue(new PayoutMail($payout));

            Session::forget('payoutdata');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return back()->withErrors([
                'otp' => __('Something Wrong Please Create Support Ticket'),
            ])->onlyInput('otp');
        }

        return Inertia::location(route('user.payout.logs'));
    }

    public function logs()
    {
        PageHeader::set()->title('Payout History')
            ->buttons([
                ['name' => 'Back', 'url' => route('user.payout.index')]
            ]);

        $payouts = Payout::where('user_id', Auth::id())->with('method')->latest()->paginate(30);

        $total_approved_requests =  Payout::where('user_id', Auth::id())->where('status', 'completed')->count();
        $total_pending_requests =  Payout::where('user_id', Auth::id())->where('status', 'pending')->count();
        $total_failed_requests =  Payout::where('user_id', Auth::id())->where('status', 'failed')->count();
        return Inertia::render(
            'User/Payout/Requests',
            compact(
                'payouts',
                'total_approved_requests',
                'total_pending_requests',
                'total_failed_requests',

            )
        );
    }

    public function invoice($id)
    {
        PageHeader::set()->title('Payout History')
            ->buttons([
                ['name' => 'Back', 'url' => route('user.payout.logs')]
            ]);

        $payout = Payout::where('user_id', Auth::id())->with('method')->where('invoice_no', $id)->first();
        abort_if(empty($payout), 404);
        $userPayoutInfo = $payout->meta ?? '';
        $method = $payout->method;

        return Inertia::render(
            'User/Payout/Invoice',
            compact('payout', 'userPayoutInfo', 'method')
        );
    }
}
