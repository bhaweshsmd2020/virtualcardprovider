<?php

namespace App\Http\Controllers\Admin;

use App\Models\Card;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Gateway;
use App\Models\CardOrder;
use App\Models\CardHolder;
use App\Models\CreditCard;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\StripeHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\CardHelper;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\CardReceipt;
use App\Models\OtherCity;
use App\Models\OtherState;

class CardOrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:card-orders');
    }
    
    public function index(Request $request)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-file"></i>&nbsp&nbsp' . __('Cardholder Data'),
                'url' => '#',
                'target' => '#cardholderDataDrawer',
            ],
            [
                'name' => '<i class="bx bx-cart"></i>&nbsp&nbsp' . __('Manual Order'),
                'url' => '#',
                'target' => '#manualOrderDrawer',
            ],

        ];
        $orders = CardOrder::query()
            ->with(['user', 'card', 'gateway', 'credit_card:id,card_order_id,card'])
            ->filterOn(['invoice_no', 'status'])
            ->filterOnRelation(['card_title', 'user_email'])
            ->latest()->paginate(20);

        $totalOrders = CardOrder::count();
        $totalPendingOrders = CardOrder::where('status', 'pending')->count();
        $totalApprovedOrders = CardOrder::where('status', 'approved')->count();
        $totalRejectedOrders = CardOrder::where('status', 'rejected')->count();
        $type = $request->type ?? 'email';
        $cardholderData = get_option('cardholder_data');

        // for orders
        $cards = Card::query()
            ->where('status', 'active')
            ->with('category:id,title,icon')
            ->get();
        $gateways = Gateway::query()->where('status', 1)->get();
        if ($request->filled('user')) {
            $findUser = User::query()->where(function ($query) {
                $query->where('email', request()->user)
                    ->orWhere('phone', request()->user);
            })->first();
        }
        return Inertia::render('Admin/CardOrder/Index', [
            'segments' => $segments,
            'orders' => $orders,
            'request' => $request,
            'totalOrders' => $totalOrders,
            'totalPendingOrders' => $totalPendingOrders,
            'totalApprovedOrders' => $totalApprovedOrders,
            'totalRejectedOrders' => $totalRejectedOrders,
            'type' => $type,
            'buttons' => $buttons,
            'cardholderData' => $cardholderData,
            'cards' => $cards,
            'gateways' => $gateways,
            'findUser' => $findUser ?? null,
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

        $creditCard = CreditCard::with([
            'user' => [
                'country',
                'state',
                'city',
            ],
            'card:id,card_variant',
            'cardHolder'
        ])->where('card_order_id', $order->id)->first();

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

        return Inertia::render('Admin/CardOrder/Show', [
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

    public function update(Request $request, $id)
    {
        $card_number = $request->card_number;
        $card_cvv = $request->card_cvv;
        $status = $request->status;

        $receipt = CardReceipt::where('order_id', $id)->first();
        if(!empty($receipt)){
            $payment_status = $request->payment_status;
            if($payment_status != 'approved'){
                return back()->with('danger', __('Invalid Payment!'));
            }
        }   
        
        if($status != 'approved'){
            return back()->with('danger', __('Card not approved'));
        }

        if(!empty($receipt)){
            CardReceipt::where('order_id', $id)->update([
                'status' => 1
            ]);
        } 
        
        $file = env('ENC_PATH');
        $jsonContents = file_get_contents($file);
        $data = json_decode($jsonContents, true);
        
        $rand = mt_rand(100000,999999);
        $string = bin2hex(openssl_random_pseudo_bytes(10));
        
        $key = $data['secret_key'];
        $plaintextnum = $rand.'-'.$card_number.'-'.$string;
        $plaintextcvv = $rand.'-'.$card_cvv.'-'.$string;
        $method = $data['method'];
        
        $hash = substr(hash('sha256', $key, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $encrypt_number = base64_encode(openssl_encrypt($plaintextnum, $method, $hash, OPENSSL_RAW_DATA, $iv));
        $encrypt_cvv = base64_encode(openssl_encrypt($plaintextcvv, $method, $hash, OPENSSL_RAW_DATA, $iv));
        
        $order = CardOrder::with([
            'user' => [
                'country',
                'state',
                'city',
            ],
            'card'
        ])->findOrFail($id);

        DB::transaction(function () use ($order, $request, $encrypt_number, $encrypt_cvv) {

            $cardHelper = new CardHelper();        
            $cardStatus = $cardHelper->cardStatus($order->invite_id);
            if(!empty($cardStatus['expiration'])){

                $expdate = $cardStatus['expiration'];
                $month = substr($expdate, 0, 2);
                $year = substr($expdate, 2, 2);

                $checkCardholder = CardHolder::where('cardholder', $cardStatus['cardholder_id'])->first();
                if(empty($checkCardholder)){
                    $cardHolder = CardHolder::create([
                        'user_id' => $order->user->id,
                        'cardholder' => $cardStatus['cardholder_id'],
                        'status' => 'active',
                    ]);
                    
                    $cardHolderId = $cardHolder->id;
                }else{
                    $cardHolderId = $checkCardholder->id;
                }

                $createCard = CreditCard::create([
                    'card' => $cardStatus['id'],
                    'user_id' => $order->user->id,
                    'card_holder_id' => $cardHolderId,
                    'card_id' => $order->card_id,
                    'card_order_id' => $order->id,
                    'status' => 'active',
                    'exp_month' => $month,
                    'exp_year' => '20'.$year,
                    'last4' => $cardStatus['last_four'],
                    'address_type' => 'user',
                    'currency' => 'usd',
                    'full_card_number' => $encrypt_number,
                    'card_cvv' => $encrypt_cvv,
                    'name_on_card' => $cardStatus['display_name'],
                    'available_limits' => $cardStatus['spending_restrictions']['amount'],
                ]);  

                $user = User::find($order->user->id);

                $template = EmailTemplate::where(['id' => '6', 'type' => 'email'])->select('subject', 'body')->first();

                $template_sub = $template->subject; 
                $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
                $template_msg = str_replace('{last_four}', $cardStatus['last_four'], $template_msg); 
                $template_msg = str_replace('{exp_date}', $month.'/20'.$year, $template_msg); 
                $template_msg = str_replace('{dashboard}', route('user.dashboard'), $template_msg); 
                $template_msg = str_replace('{apple_logo}', '<img src="' . url('assets/images/applewallet.png') . '" style="width: 150px;" alt="apple logo">', $template_msg);
                $template_msg = str_replace('{google_logo}', '<img src="' . url('assets/images/googlewallet.png') . '" style="width: 150px;" alt="google logo">', $template_msg);

                $emailHelper = new EmailHelper();
                $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

                CardOrder::where('id', $order->id)->update(['status' => 'approved']);

                Notification::create([
                    'user_id' => $user->id,
                    'url' => route('user.credit-cards.show', $createCard->id),
                    'title' => __('Card request has been approved'),
                    'comment' => __('Your Card request has been approved successfully'),
                    'for_admin' => 0,
                ]);
            }
        });

        return back()->with('success', __('Order status updated successfully'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'card_id' => 'required',
            'payment_id' => 'required',
            'user_id' => 'required',
            'gateway_id' => 'required',
            'amount' => 'required',
        ]);
        DB::transaction(function () use ($request) {
            $tax = get_option('tax');
            $order = CardOrder::create([
                'status' => 'pending',
                'card_id' => $request->card_id,
                'payment_id' => $request->payment_id,
                'user_id' => $request->user_id,
                'gateway_id' => $request->gateway_id,
                'amount' => $request->amount,
                'tax' => $tax,
            ]);
            Notification::create([
                'user_id' => auth()->id(),
                'title' => __('New Card Order submitted'),
                'comment' => __('A  Card Order has been submitted'),
                'url' => route('user.card-orders.show', $order->id),
                'for_admin' => 0,
            ]);
        });

        return back()->with('success', __('Card Order Created Successfully'));
    }
    public function updateStatus(Request $request, $id)
    {
        $order = CardOrder::findOrFail($id);
        $order->update([
            'status' => $request->status,
        ]);

        return back();
    }
}
