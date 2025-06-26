<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request, $id)
    {  
        if($request->user() == null){
            $user = User::find($id);
            if ($user->markEmailAsVerified()) {
                event(new Verified($request->user()));
            } 
            return redirect()->intended(route($user->getDashboardRoute()));
        }

        if (config('feature.email_verification') == false || $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route($request->user()->getDashboardRoute()));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }        

        return redirect()->intended(url('/login'));
    }
}
