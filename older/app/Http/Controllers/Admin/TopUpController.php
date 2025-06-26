<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\TopUp;
use App\Models\Wallet;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\VirtualCurrency;
use App\Http\Controllers\Controller;
use App\Models\TopupReceipt;

class TopUpController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:top-up');
    }
    public function index(Request $request)
    {
        $segments = request()->segments();
        $topUps = TopUp::query()
            ->filterOn(['invoice_no', 'status'])
            ->filterOnRelation(['user_email'])
            ->with('user', 'gateway')
            ->latest()->paginate(20);

        $totalTopUps = TopUp::count();
        $totalAmount = TopUp::sum('amount');
        $totalApprovedTopUps = TopUp::where('status', 'approved')->count();
        $totalRejectedTopUps = TopUp::where('status', 'rejected')->count();
        $type = $request->type ?? 'email';

        return Inertia::render('Admin/TopUp/Index', [
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
        $order = TopUp::with('user', 'gateway')->findOrFail($id);
        $invoice_data = get_option('invoice_data');

        $receipt = TopupReceipt::where('order_id', $order->id)->first();
        if(!empty($receipt)){
            $receiptDetail = $receipt;
        }else{
            $receiptDetail = null;
        }

        $meta = $order->meta ?? '';
        return Inertia::render('Admin/TopUp/Show', [
            'order' => $order,
            'invoice_data' => $invoice_data,
            'meta' => $meta,
            'receiptDetail' => $receiptDetail,
        ]);
    }

    public function update(Request $request, $id)
    {
        $topUp = TopUp::with('user', 'gateway')->findOrFail($id);
        $user = $topUp->user;
        if ($request->status === 'approved') {

            $receipt = TopupReceipt::where('order_id', $id)->first();
            if(!empty($receipt)){
                TopupReceipt::where('order_id', $id)->update([
                    'status' => 1
                ]);
            } 
            
            if ($topUp->wallet_id) {
                $wallet = Wallet::query()
                    ->with('currency')
                    ->where('id', $topUp->wallet_id)
                    ->where('user_id', $user->id)
                    ->first();
                $defaultCurrency = VirtualCurrency::query()
                    ->with('rates.exchange_currency')
                    ->where('is_default', 1)
                    ->firstOrFail();
                if ($wallet->currency->currency == $defaultCurrency->currency) {
                    $amount = $topUp->requested_amount;
                } else {
                    $amount = $topUp->requested_amount;
                }
                ;

                $wallet->balance += $amount;
                $wallet->save();
            } else {
                $user->balance += $topUp->requested_amount;
                $user->save();
            }
            $topUp->update(['status' => 'approved']);
        }
        if ($request->status == 'rejected') {
            $topUp->update(['status' => 'rejected']);
        }        
        
        Notification::create([
            'user_id' => $user->id,
            'title' => __('Topup Status Updated'),
            'comment' => __('Topup status updated to :status', ['status' => $request->status]),
            'url' => route('user.top-up.show', $topUp->id),
            'for_admin' => 0,
        ]);

        return back()->with('success', 'Updated successfully');
    }
}
