<?php

namespace App\Http\Controllers\User;

use App\Helpers\Toastr;
use App\Http\Controllers\Controller;
use App\Models\CardHolder;
use App\Models\CreditCard;
use App\Models\Gateway;
use App\Services\StripeHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\CardHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\CardOrder;
use App\Models\CardlimitRequest;
use App\Models\User;
use App\Models\CardTopup;
use Illuminate\Support\Str;
use App\Models\CardLimit;
use App\Models\Card;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\EmailConfig;
use App\Models\Notification;
use App\Models\OtherCity;
use App\Models\OtherState;

class CreditCardController extends Controller
{
    public function index(Request $request)
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp&nbsp' . __('Apply New Prepaid Card'),
                'url' => route('user.card-issue.create'),
            ]
        ];
        $creditCards = CreditCard::query()
            ->filterOn(['title', 'last4', 'status'])
            ->where('user_id', auth()->id())
            ->with(['user', 'card:id,card_variant'])
            ->paginate(12);

        $totalCards = CreditCard::where('user_id', auth()->id())->count();
        $totalPendingCards = CreditCard::where('status', 'pending')->count();
        $totalApprovedCards = CreditCard::where('status', 'approved')->count();
        $totalRejectedCards = CreditCard::where('status', 'rejected')->count();
        $card_intro_details = get_option('card_intro_details');
        return Inertia::render('User/CreditCard/Index', [
            'buttons' => $buttons,
            'creditCards' => $creditCards,
            'totalCards' => $totalCards,
            'totalPendingCards' => $totalPendingCards,
            'totalApprovedCards' => $totalApprovedCards,
            'totalRejectedCards' => $totalRejectedCards,
            'request' => $request,
            'card_intro_details' => $card_intro_details
        ]);
    }

    public function show($id)
    {     
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp&nbsp' . __('Topup account'),
                'url' => route('user.top-up.create'),
            ]
        ];
        $creditCard = CreditCard::with(['user' => [
            'country',
            'state',
            'city',
        ], 'card_order', 'virtual_card'])->findOrFail($id);

        $cardholder_data = get_option('cardholder_data');
        $gateways = Gateway::query()->where('status', 1)->get();

        $cardHelper = new CardHelper();
        $cardDetails = $cardHelper->cardDetails($creditCard->card);
        $cardTransactions = $cardHelper->cardTransactions($creditCard->card);
        $spandTransactions = $cardHelper->spandTransactions($creditCard->card);

        $user_id = auth()->user()->id;       

        $prepaidcard = Card::where('id', $creditCard->card_id)->first();
        $cardlimit = CardLimit::where('user_id', $user_id)->where('card_type', $prepaidcard->id)->first();
        if(!empty($cardlimit)){
            $prepaidcard->card_limit = $cardlimit->limit;  
            $prepaidcard->topup_limit = $cardlimit->topup_limit;  
            $prepaidcard->spending_limit = $cardlimit->spending_limit;
        }   
        
        $othercity = OtherCity::where('user_id', $user_id)->first();
        $otherstate = OtherState::where('user_id', $user_id)->first();
        
        $stripeCurrency = 'usd';
        return Inertia::render('User/CreditCard/Show', [
            'creditCard' => $creditCard,
            'cardholder_data' => $cardholder_data,
            'buttons' => $buttons,
            'gateways' => $gateways,
            'stripe_currency' => $stripeCurrency,
            'card_details' => $cardDetails,
            'transactions' => $cardTransactions,
            'prepaidcard' => $prepaidcard,                      
            'other_city' => $othercity,
            'other_state' => $otherstate,
        ]);
    }    

    public function activateCards()
    {
        $user = Auth::user();

        $orders = CardOrder::where('user_id', $user->id)->where('status', 'pending')->get();
        foreach($orders as $order){
            $cardHelper = new CardHelper();        
            $cardStatus = $cardHelper->cardStatus($order->invite_id);
            if(!empty($cardStatus['expiration'])){

                $expdate = $cardStatus['expiration'];
                $month = substr($expdate, 0, 2);
                $year = substr($expdate, 2, 2);

                $checkCardholder = CardHolder::where('cardholder', $cardStatus['cardholder_id'])->first();
                if(empty($checkCardholder)){
                    $cardHolder = CardHolder::create([
                        'user_id' => $user->id,
                        'cardholder' => $cardStatus['cardholder_id'],
                        'status' => 'active',
                    ]);
                    
                    $cardHolderId = $cardHolder->id;
                }else{
                    $cardHolderId = $checkCardholder->id;
                }

                CreditCard::create([
                    'card' => $cardStatus['id'],
                    'user_id' => $user->id,
                    'card_holder_id' => $cardHolderId,
                    'card_id' => $order->card_id,
                    'card_order_id' => $order->id,
                    'status' => 'active',
                    'exp_month' => $month,
                    'exp_year' => '20'.$year,
                    'last4' => $cardStatus['last_four'],
                    'address_type' => 'user',
                    'currency' => 'usd',
                    'name_on_card' => $cardStatus['display_name']
                ]);  

                CardOrder::where('id', $order->id)->update(['status' => 'approved']);
            }
        }      
                
        $message =  __('Card activated successfully.');
        Toastr::success($message);
        return to_route('user.credit-cards.index')->with('success', $message);
    }

    public function transactions()
    {
        $cardHolder = CardHolder::where('user_id', auth()->id())->first();
        $cardHelper = new CardHelper();
        $cardTransactions = $cardHelper->allCardsTransactions($cardHolder->cardholder);
        
        return Inertia::render('User/Transaction/Index', [
            'transactions' => $cardTransactions,
        ]);
    }

    public function authorizations(StripeHelper $stripeHelper)
    {
        $cardholder = CardHolder::query()->where('user_id', auth()->id());
        if (!$cardholder->exists()) {
            return Inertia::render('User/Authorization/Index', [
                'transactions' => ['data' => []],
            ]);
        }

        $stripeData = $stripeHelper->authorizations('user.credit-card.authorizations', $cardholder->first()->cardholder);
        $stripeCurrency = env('STRIPE_CURRENCY', 'usd');
        return Inertia::render('User/Authorization/Index', [
            'authorizations' => $stripeData['authorizations'],
            'has_more' => $stripeData['has_more'],
            'next' => $stripeData['next'],
            'prev' => $stripeData['prev'],
            'stripe_currency' => $stripeCurrency
        ]);
    }

    public function updateStatus($id, $status)
    {
        if ($status == 'active') {
            $updateStatus = 'inactive';
            $message = 'Card deactivated successfully';
            $type = 'warning';
        } else {
            $updateStatus = 'active';
            $message = 'Card activated successfully';
            $type = 'success';
        }

        $creditCard = CreditCard::findOrFail($id);

        $cardHelper = new CardHelper();
        $cardHelper->cardStatusUpdate($creditCard->card, $updateStatus);

        $creditCard->update(['status' => $updateStatus]);

        return redirect()->route('user.credit-cards.show', $id)->with('flash', [
            'type' => $type,
            'message' => $message
        ]);
    }

    public function increaseLimit(Request $request)
    {
        $card_id = $request->credit_card_id;
        $sub_total = $request->sub_total;
        $total = $request->total;  
        
        $cardHelper = new CardHelper();
        $cardUsesLimit = $cardHelper->cardTopup($card_id, $sub_total);

        User::where('id', auth()->user()->id)->update(['balance' => auth()->user()->balance - $total]);    
        
        $topUp = new CardTopup();
        $topUp->user_id             = auth()->id();
        $topUp->payment_id          = 'pi_' . Str::random(8) . strtoupper(Str::random(16));
        $topUp->gateway_id          = 13;
        $topUp->wallet_id           = null;
        $topUp->amount              = $total;
        $topUp->status              = 'approved';
        $topUp->type                = 'balance';
        $topUp->order_uid           = null;
        $topUp->transaction_fee     = $total - $sub_total;
        $topUp->rate                = 1;
        $topUp->requested_amount    = $sub_total;
        $topUp->meta                = null;
        $topUp->save();

        $topupId = $topUp->id;

        $card = CreditCard::where('id', $card_id)->first();

        CreditCard::where('id', $card_id)->update(['available_limits' => $card->available_limits + $sub_total]);

        $user = User::where('id', auth()->user()->id)->first();        

        // User Email
        $template = EmailTemplate::where(['id' => '12', 'type' => 'email'])->select('subject', 'body')->first();

        $template_sub = $template->subject;
        $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
        $template_msg = str_replace('{card}', $card->last4, $template_msg); 
        $template_msg = str_replace('{amount}', $total, $template_msg);
        $template_msg = str_replace('{date}', now(), $template_msg); 

        $emailHelper = new EmailHelper();
        $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

        // Admin Email
        $emailConfig = EmailConfig::where(['email_protocol' => 'smtp', 'status' => '1'])->first();
        $template = EmailTemplate::where(['id' => '13', 'type' => 'email'])->select('subject', 'body')->first();

        $template_sub = $template->subject;
        $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
        $template_msg = str_replace('{card}', $card->last4, $template_msg); 
        $template_msg = str_replace('{amount}', $total, $template_msg);
        $template_msg = str_replace('{date}', now(), $template_msg);

        $emailHelper = new EmailHelper();
        $verify = $emailHelper->sendEmail($emailConfig->notification_email, $template_sub, $template_msg);

        Notification::create([
            'user_id' => $user->id,
            'title' => __('New Card Topup'),
            'comment' => __('A User has topup a card'),
            'url' => route('admin.credit-card.topupdetails', $topupId),
        ]);

        return redirect()->route('user.credit-cards.show', $request->credit_card_id)->with('flash', [
            'type' => 'success',
            'message' => 'Card topup done successfully'
        ]);
    }

    public function getFullCardDetails($id)
    {
        $creditCard = CreditCard::findOrFail($id);
        $encrypted_number = $creditCard->full_card_number;
        $encrypted_cvv = $creditCard->card_cvv;
        
        $file = env('ENC_PATH');
        $jsonContents = file_get_contents($file);
        $data = json_decode($jsonContents, true);
        
        $method = $data['method'];
        $key = $data['secret_key'];
        $hash = substr(hash('sha256', $key, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        $decrypt_number = openssl_decrypt(base64_decode($encrypted_number), $method, $hash, OPENSSL_RAW_DATA, $iv);
        $explode_number = explode("-",$decrypt_number);        
        foreach($explode_number as $key => $e){
            if($key > 0){ 
                $new_number_data[] = $e;
            }
        }        
        foreach (array_slice($new_number_data, 0, count($new_number_data) - 1) as $key => $val) {
            $dec_num['val'.$key] = $val;
        }

        $decrypt_cvv = openssl_decrypt(base64_decode($encrypted_cvv), $method, $hash, OPENSSL_RAW_DATA, $iv);
        $explode_cvv = explode("-",$decrypt_cvv);        
        foreach($explode_cvv as $key => $e){
            if($key > 0){ 
                $new_cvv_data[] = $e;
            }
        }        
        foreach (array_slice($new_cvv_data, 0, count($new_cvv_data) - 1) as $key => $val) {
            $dec_cvv['val'.$key] = $val;
        }

        return response()->json([
            'full_card_number' => $dec_num['val0'],
            'card_cvv' => $dec_cvv['val0'],
        ]);
    }

    public function updateCard(Request $request)
    {
        $card_id = $request->card_id;
        $name_on_card = $request->name_on_card;

        $card = CreditCard::findOrFail($card_id);

        $cardHelper = new CardHelper();
        $cardDetails = $cardHelper->cardUpdate($card->card, $name_on_card);
        
        CreditCard::where('id', $card_id)->update(['name_on_card' => $name_on_card]);

        return back()->with('success', 'Card updated successfully');
    }

    public function topups(Request $request)
    {
        $segments = request()->segments();
        $topUps = CardTopup::query()
            ->filterOn(['invoice_no', 'status', 'amount'])
            ->where('user_id', auth()->id())
            ->with('user', 'gateway')
            ->latest()->paginate(20);

        $totalTopUps = CardTopup::where('user_id', auth()->id())->count();
        $totalAmount = CardTopup::where('user_id', auth()->id())->sum('amount');
        $totalApprovedTopUps = CardTopup::where('user_id', auth()->id())->where('status', 'approved')->count();
        $totalRejectedTopUps = CardTopup::where('user_id', auth()->id())->where('status', 'rejected')->count();
        $type = $request->type ?? 'email';

        return Inertia::render('User/CreditCard/TopUp', [
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

    public function topupdetails($id)
    {
        $order  = CardTopup::with('user', 'gateway')->findOrFail($id);
        $invoice_data = get_option('invoice_data');

        $meta = $order->meta ?? '';
        return Inertia::render('User/CreditCard/Topupdetails', [
            'order' => $order,
            'invoice_data' => $invoice_data,
            'meta' => $meta,
        ]);
    }

    public function balance($id)
    {
        $creditCard = CreditCard::findOrFail($id);

        $cardHelper = new CardHelper();
        $spandTransactions = $cardHelper->cardTransactions($creditCard->card);
        $cardDetails = $cardHelper->cardDetails($creditCard->card);
        
        $totalSpend = 0;
        foreach($spandTransactions['data'] as $spandTransaction){
            if($spandTransaction['state'] == 'CLEARED'){
                $totalSpend += $spandTransaction['amount'];
            }
        }   
        
        $available_balance = $cardDetails['spending_restrictions']['amount'] - $totalSpend;

        CreditCard::where('id', $id)->update(['available_limits' => number_format($available_balance, 2, '.', '')]);
        
        return response()->json(['updated_balance' => number_format($available_balance, 2, '.', '')]);
    }
}
