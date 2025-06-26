<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Toastr;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CreditCard;
use App\Models\Deposit;
use App\Models\Notification;
use App\Services\StripeHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:deposits');
    }
    public function index(Request $request)
    {
        $segments = request()->segments();
        $deposits = Deposit::query()
            ->filterOn(['invoice_no', 'status'])
            ->filterOnRelation(['user_email'])
            ->with('user', 'gateway')->latest()->paginate(20);

        $totalDeposits = Deposit::count();
        $totalAmount = Deposit::sum('amount');
        $totalApprovedDeposits = Deposit::where('status', 'approved')->count();
        $totalRejectedDeposits = Deposit::where('status', 'rejected')->count();
        $type = $request->type ?? 'email';

        return Inertia::render('Admin/Deposit/Index', [
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
        return Inertia::render('Admin/Deposit/Show', [
            'order' => $order,
            'invoice_data' => $invoice_data,
            'meta' => $order->meta,
            'creditCard' => $creditCard,
        ]);
    }

    public function update(Request $request, $id)
    {
        $deposit = Deposit::with(['user'])->findOrFail($id);

        $isUserHasCreditCard = CreditCard::where('user_id', $deposit->user->id);
        if (!$isUserHasCreditCard->exists()) {
            return Toastr::danger('No Prepaid Card Available');
        }
        if ($request->status == 'approved') {
            $creditCard = $isUserHasCreditCard->first();
            if ($creditCard->status == 'active') {
                $creditCard->increment('spending_limits', $deposit->requested_amount);
                $deposit->update(['status' => 'approved']);
            } else {
                return back()->with('danger', 'Prepaid Card is not active');
            }
        }
        if ($request->status == 'rejected') {
            $deposit->update(['status' => 'rejected']);
        }
        Notification::create([
            'user_id' => auth()->id(),
            'title' => __('Deposit Status Updated'),
            'comment' => __('Deposit status updated to :status', ['status' => $request->status]),
            'url' => route('user.deposits.show', $deposit->id),
            'for_admin' => 0,
        ]);
        return back()->with('success', 'Deposits status updated');
    }
}
