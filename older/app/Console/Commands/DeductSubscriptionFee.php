<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\EmailConfig;
use App\Models\User;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Order;

class DeductSubscriptionFee extends Command
{
    protected $signature = 'subscription:deduct';
    protected $description = 'Deduct subscription fee when a plan expires and send notification email';

    public function handle()
    {
        $users = User::where('will_expire', '<=', now()->toDateString())->whereNotNull('plan_id')->get();

        foreach ($users as $user) {
            try {
                $plan = Plan::find($user->plan_id);
                
                $tax      = get_option('tax');
                $tax      = $tax > 0 ? ($plan->price / 100) * $tax : 0;
                $total    = (float)$tax + $plan->price;
                
                if ($user->balance >= $total) {
                    $user->balance -= $total;
                    $user->plan_data = $plan->data;
                    $user->plan_id = $plan->id;
                    $user->will_expire = now()->addDays($plan->days);
                    $user->save();

                    $order = new Order;
                    $order->plan_id     = $plan->id;
                    $order->payment_id  = 'pi_' . Str::random(8) . strtoupper(Str::random(16));
                    $order->user_id     = $user->id;
                    $order->gateway_id  = 13;
                    $order->amount      = $total;
                    $order->tax         = $tax;
                    $order->status      = 'approved';
                    $order->will_expire = now()->addDays($plan->days);
                    $order->order_uid   = null;
                    $order->meta        = null;
                    $order->save();

                    // Send email notification
                    $template = EmailTemplate::where(['id' => 11, 'type' => 'email'])->select('subject', 'body')->first();

                    if ($template) {
                        // Prepare the subject and body by replacing placeholders
                        $template_sub = $template->subject;
                        $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
                        $template_msg = str_replace('{plan}', $plan->title, $template_msg);
                        $template_msg = str_replace('{amount}', $plan->price, $template_msg);
                        $template_msg = str_replace('{expiry_date}', now()->addDays($plan->days)->toDateString(), $template_msg);

                        // Use EmailHelper to send the email
                        $emailHelper = new EmailHelper();
                        $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

                        Notification::create([
                            'user_id' => $user->id,
                            'url' => route('user.dashboard'),
                            'title' => __('Subscription renewal reminder'),
                            'comment' => __('Your subscription has been renewed'),
                            'for_admin' => 0,
                        ]);
                    } else {
                        Log::error("Email template not found for ID: 11.");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error processing user ID: {$user->id}, Error: " . $e->getMessage());
            }
        }

        Log::info('Subscription fee deduction completed.');
    }
}
