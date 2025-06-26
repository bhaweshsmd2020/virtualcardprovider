<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use App\Models\User;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        try {
            $user = User::where('id', auth()->user()->id)->first();

            $verificationUrl = $this->verificationUrl($user);                

            $template = EmailTemplate::where(['id' => '1', 'type' => 'email'])->select('subject', 'body')->first();

            $template_sub = $template->subject;
            $template_msg = str_replace('{user}', 'User', $template->body);
            $template_msg = str_replace('{username}', $user->username, $template_msg);
            $template_msg = str_replace('{activate_url}', $verificationUrl, $template_msg);

            $emailHelper = new EmailHelper();
            $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);
            
        } catch (\Throwable $th) {
           
        }
        
        return back()->with('status', 'verification-link-sent');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
