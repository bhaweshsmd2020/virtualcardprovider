<?php

namespace App\Http\Controllers\User;

use App\Helpers\Toastr;
use App\Helpers\CardHelper;
use App\Models\Gateway;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardOrder;
use App\Traits\Uploader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\CreditCard;
use App\Models\CardHolder;
use App\Models\CardEmail;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\EmailConfig;
use App\Models\CardReceipt;

class CardPayController extends Controller
{
    use Uploader;
    public function store(Request $request)
    {
        $user = Auth::user();

        $user->validatePlan('cards');

        $card = Card::where('status', 'active')->findOrFail($request->card_id);
        $subtotal  = $request->subtotal;
        $card_fee = ($subtotal * $card->card_fee) / 100;
        $total  = $card_fee + $subtotal;
        $name_on_card  = $request->name_on_card;
        $pay_type = $request->pay_type;
        $phone = $request->phone;
        $comment = $request->comment;  

        if ($card->min_deposit > $subtotal) {
            return redirect()->back()->with('danger', __('The minimum deposit amount is ' . $card->min_deposit));
        }
        if ($card->max_deposit < $subtotal) {
            return redirect()->back()->with('danger', __('The maximum deposit amount is ' . $card->max_deposit));
        }

        if ($user->balance < $total) {
            return redirect()->back()->with('danger', __('Insufficient Balance.'));
        }

        $cardHelper = new CardHelper();
        $creditCard = $cardHelper->createCard($subtotal, $name_on_card);

        if($pay_type == 'account'){          
            $gateway_id = '13';
        }else{       
            $gateway_id = '14';
        }  

        $order = CardOrder::create([
            'card_id' => $card->id,
            'payment_id' => 'pi_' . Str::random(8) . strtoupper(Str::random(16)),
            'user_id' => $user->id,
            'gateway_id' => $gateway_id,
            'amount' => $total,
            'order_uid' => session('order_uid'),
            'ip_address' => request()->ip(),
            'status' => 'pending',
            'deposit_fee' => $card_fee,
            'meta' => null,
            'payment_status' => 'paid',
            'invite_id' => $creditCard
        ]); 
        
        if($pay_type == 'account'){   
            User::where('id', $user->id)->update(['balance' => $user->balance - $total]);    
        }else{
            $receipt = new CardReceipt();
            $receipt->order_id = $order->id;
            $receipt->image = $this->saveFile($request, 'image');
            $receipt->comments = $comment;
            $receipt->save();    
        }            

        $assigned_email = CardEmail::where('user_id', $user->id)->first();
        $emailConfig = EmailConfig::where(['email_protocol' => 'smtp', 'status' => '1'])->first();

        $template = EmailTemplate::where(['id' => '5', 'type' => 'email'])->select('subject', 'body')->first();

        $template_sub = $template->subject;
        $template_msg = str_replace('{today}', now(), $template->body); 
        $template_msg = str_replace('{name}', $user->first_name . ' ' . $user->last_name, $template_msg);
        $template_msg = str_replace('{email}', $user->email, $template_msg); 
        $template_msg = str_replace('{phone}', $user->country_code.$user->phone, $template_msg); 
        $template_msg = str_replace('{ramp_email}', $assigned_email->email, $template_msg); 

        $emailHelper = new EmailHelper();
        $verify = $emailHelper->sendEmail($emailConfig->notification_email, $template_sub, $template_msg);

        Notification::create([
            'user_id' => $user->id,
            'title' => __('New Card Purchased'),
            'comment' => __('A User has purchased a card is under process'),
            'url' => route('admin.card-orders.show', $order->id),
        ]);

        $message =  __('Prepaid card requested successfully');
        return redirect('/user/card-orders?success=' . urlencode($message));
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
        $card = Card::where('status', 1)->findOrFail(Session::get('card_id'));

        Session::forget('payment_info');
        Session::forget('call_back');
        Session::forget('card_id');

        DB::transaction(function () use ($paymentInfo, $card) {
            $order = new CardOrder;
            $order->card_id     = $card->id;
            $order->payment_id  = $paymentInfo['payment_id'];
            $order->user_id     = auth()->id();
            $order->gateway_id  = $paymentInfo['gateway_id'];
            $order->amount      = $paymentInfo['amount'];

            $order->order_uid      = session('order_uid');

            $order->ip_address  = request()->ip();
            $order->status      = $paymentInfo['status'] ?? 1;
            $order->deposit_fee =  Session::get('deposit_fee');
            if (isset($paymentInfo['meta'])) {
                $order->meta    = $paymentInfo['meta'];
            }
            $order->save();

            Notification::create([
                'user_id' => auth()->id(),
                'title' => __('New Card Purchased'),
                'comment' => __('A User has purchased a card'),
                'url' => route('admin.card-orders.show', $order->id),
            ]);
        });

        $message =  __('Your payment is complete admin will review this payment manually for approval.');

        Toastr::success($message);

        return to_route('user.card-orders.index')->with('success', $message);
    }

    private function failed()
    {
        Session::forget('payment_info');
        Session::forget('call_back');
        Session::flash('error', true);

        $message = __('Your payment request is failed');

        Toastr::danger($message);
        return to_route('user.card-orders.index')->with('danger', $message);;
    }
}
