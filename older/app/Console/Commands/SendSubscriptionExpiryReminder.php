<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User; // Assuming you have a User model
use App\Mail\SubscriptionExpiryReminder; // Assuming you have this Mailable
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\EmailConfig;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class SendSubscriptionExpiryReminder extends Command
{
    protected $signature = 'subscription:reminder';
    protected $description = 'Send subscription expiry reminders to users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $reminderDays = [7, 3, 1];
        
        foreach ($reminderDays as $days) {
            // Fetch users whose subscriptions are expiring on the exact date
            $users = User::whereDate('will_expire', now()->addDays($days)->toDateString())->get();

            foreach ($users as $user) {
                // Fetch email template from the database
                $template = EmailTemplate::where(['id' => 9, 'type' => 'email'])->select('subject', 'body')->first();

                if ($template) {
                    // Prepare the subject and body by replacing placeholders
                    $template_sub = $template->subject;
                    $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
                    $template_msg = str_replace('{days}', $days, $template_msg);

                    // Use EmailHelper to send the email
                    $emailHelper = new EmailHelper();
                    $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

                    Notification::create([
                        'user_id' => $user->id,
                        'url' => route('user.subscription.index'),
                        'title' => __('Subscription Expiring Reminder'),
                        'comment' => __('Your subscription is expiring soon'),
                        'for_admin' => 0,
                    ]);
                } else {
                    Log::error("Email template not found for ID: 9.");
                }
            }
        }

        $this->info('Subscription expiry reminders sent successfully.');
    }
}
