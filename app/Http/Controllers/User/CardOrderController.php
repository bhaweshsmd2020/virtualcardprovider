<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Models\CardOrder;
use App\Models\CreditCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\CardHelper;
use App\Models\CardReceipt;
use App\Models\OtherCity;
use App\Models\OtherState;

class CardOrderController extends Controller
{
    public function index(Request $request)
    {
        $segments = request()->segments();

        $orders = CardOrder::query()->where('user_id', auth()->id());

        if (!empty($request->search)) {
            if ($request->type == 'email') {
                $orders = $orders->whereHas('user', function ($q) use ($request) {
                    return $q->where('email', $request->search);
                });
            } else {
                $orders = $orders->where($request->type, 'LIKE', '%' . $request->search . '%');
            }
        }

        $orders = $orders->with('user', 'card', 'gateway')->latest()->paginate(20);

        $totalOrders = CardOrder::where('user_id', auth()->id())->count();
        $totalPendingOrders = CardOrder::where('status', 'pending')->where('user_id', auth()->id())->count();
        $totalApprovedOrders = CardOrder::where('status', 'approved')->where('user_id', auth()->id())->count();
        $totalRejectedOrders = CardOrder::where('status', 'rejected')->where('user_id', auth()->id())->count();
        $type = $request->type ?? 'email';

        return Inertia::render('User/CardOrder/Index', [
            'segments' => $segments,
            'orders' => $orders,
            'request' => $request,
            'totalOrders' => $totalOrders,
            'totalPendingOrders' => $totalPendingOrders,
            'totalApprovedOrders' => $totalApprovedOrders,
            'totalRejectedOrders' => $totalRejectedOrders,
            'type' => $type,
        ]);
    }

    public function show($id)
    {
        $order = CardOrder::with([
            'user' => [
                'country:id,name',
                'state:id,name',
                'city:id,name',
            ],
            'card',
            'gateway'
        ])->findOrFail($id);

        $receipt = CardReceipt::where('order_id', $order->id)->first();
        if(!empty($receipt)){
            $receiptDetail = $receipt;
        }else{
            $receiptDetail = null;
        }
        
        $creditCard = CreditCard::with(['user' => [
            'country',
            'state',
            'city',
        ], 'card:id,card_variant', 'cardHolder'])
            ->where('card_order_id', $order->id)->first();

        $invoice_data = get_option('invoice_data');

        $cardHelper = new CardHelper();

        if(empty($creditCard)){        
            $cardStatus = $cardHelper->cardStatus($order->invite_id);
            $cardDetails = $cardHelper->cardDetails($cardStatus['id']);
            $cardTransactions = $cardHelper->cardTransactions($cardStatus['id']);
        }else{
            $cardDetails = $cardHelper->cardDetails($creditCard->card);
            $cardTransactions = $cardHelper->cardTransactions($creditCard->card);
        } 

        $othercity = OtherCity::where('user_id', $order->user_id)->first();
        $otherstate = OtherState::where('user_id', $order->user_id)->first();

        return Inertia::render('User/CardOrder/Show', [
            'order' => $order,
            'invoice_data' => $invoice_data,
            'meta' => $order->meta,
            'creditCard' => $creditCard ?? null,
            'card_details' => $cardDetails,
            'receiptDetail' => $receiptDetail,                     
            'other_city' => $othercity,
            'other_state' => $otherstate,
        ]);
    }
}
