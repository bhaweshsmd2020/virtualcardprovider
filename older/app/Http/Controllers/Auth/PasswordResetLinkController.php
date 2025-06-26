<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'authPages' => get_option('auth_pages', true)
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with this email.']);
        }
    
        $token = Password::createToken($user);
        $reset_link = url('/reset-password/' . $token);
        $expiration_time = config('auth.passwords.users.expire') . ' minutes';

        $template = EmailTemplate::where(['id' => '8', 'type' => 'email'])->select('subject', 'body')->first();

        $template_sub = $template->subject;
        $template_msg = str_replace('{user}', $user->first_name, $template->body);
        $template_msg = str_replace('{reset_link}', $reset_link, $template_msg);
        $template_msg = str_replace('{expiration_time}', $expiration_time, $template_msg);

        $emailHelper = new EmailHelper();
        $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);
        
        return back();
    }
}
