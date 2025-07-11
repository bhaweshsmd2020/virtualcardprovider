<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Payout;
use App\Models\Wallet;
use App\Mail\PayoutMail;
use Illuminate\Http\Request;
use App\Models\UserPayoutMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class PayoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:payouts');
    }

    public function index(Request $request)
    {

        $data['total_payouts'] = Payout::count();
        $data['total_approved'] = Payout::where('status', 'approved')->count();
        $data['total_rejected'] = Payout::where('status', 'rejected')->count();
        $data['total_pending'] = Payout::where('status', 'pending')->count();
        $payouts = Payout::with('user', 'method')->latest()
            ->when(request('status') == 'approved', function ($q) {
                return  $q->where('status', 'approved');
            })
            ->when(request('status') == 'rejected', function ($q) {
                return $q->where('status', 'rejected');
            })
            ->when(request('status') == 'pending', function ($q) {
                return $q->where('status', 'pending');
            });
        if ($request->type == 'email' && $request->search != null) {
            $payouts = $payouts->whereHas('user', function ($q) use ($request) {
                return $q->where('email', $request->search);
            });
        }
        if ($request->type == 'invoice_no' && $request->search != null) {
            $payouts = $payouts->where('invoice_no', $request->search);
        }

        $data['payouts'] = $payouts->paginate(30);

        $segments = request()->segments();
        $buttons = [];
        $query = $request->src ?? '';
        $type = $request->type ?? 'invoice_no';


        return  Inertia::render('Admin/Payouts/Index', compact('segments', 'buttons', 'data', 'query', 'type', 'request'));
    }

    public function show(Payout $payout)
    {
        $payout->load('user', 'method', 'wallet.currency');

        $segments = request()->segments();
        $buttons = [
            [
                'name' => __('Back'),
                'url' => route('admin.payouts.index')
            ]
        ];

        return  Inertia::render('Admin/Payouts/Show', [
            'payout' => $payout,
            'segments' => $segments,
            'buttons' => $buttons
        ]);
    }

    public function update(Request $request, $id)
    {
        $payout = Payout::find($id);
        $payout->status = $request->status;
        $payout->save();

        if (isset($request->paymentStatus)) {
            if ($payout->wallet_id) {
                $wallet = Wallet::query()
                    ->where('user_id', $payout->user_id)
                    ->where('id', $payout->wallet_id)->first();
                $wallet->increment('balance', $payout->requested_amount);
            } else {
                $user = User::where('id', $payout->user_id)->first();
                $user->balance = $user->balance + $payout->amount;
                $user->save();
            }
        }

        if (env('QUEUE_MAIL')) {
            Mail::to($payout->user->email)->queue(new PayoutMail($payout));
        } else {
            Mail::to($payout->user->email)->send(new PayoutMail($payout));
        }

        return back();
    }
    public function approved(Request $request)
    {
        $payout = Payout::with('user')->find($request->payout);

        // Send Email to admin
        if (env('QUEUE_MAIL')) {
            Mail::to($payout->user->email)->queue(new PayoutMail($payout));
        } else {
            Mail::to($payout->user->email)->send(new PayoutMail($payout));
        }

        $payout->update([
            'status' => 'approved'
        ]);

        return to_route('admin.payouts.index');
    }

    public function reject(Request $request)
    {
        $payout = Payout::with('user')->find($request->payout);

        $payout->update([
            'status' => 'rejected'
        ]);

        // Send Email to admin
        if (env('QUEUE_MAIL')) {
            Mail::to($payout->user->email)->queue(new PayoutMail($payout));
        } else {
            Mail::to($payout->user->email)->send(new PayoutMail($payout));
        }

        return response()->json([
            'message' => __('Payout rejected successfully.'),
            'redirect' => route('admin.payouts.index')
        ]);
    }
}
