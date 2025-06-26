<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorAuthenticationController extends Controller
{

    public function index(Request $request)
    {
        $authenticator = app(Authenticator::class)->boot($request);
        if ($authenticator->isAuthenticated()) {
            return redirect()->route('user.dashboard');
        }

        return Inertia::render('Auth/TwoFactorAuthentication', [
            'authPages' => get_option('auth_pages', true)
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'one_time_password' => ['required']
        ]);

        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        $one_time_password = $request->get('one_time_password');
        $secret = $request->get('secret', $user->google2fa_secret);

        $google2fa = new Google2FA();
        $timestamp = $google2fa->verifyKey($secret, $one_time_password);
        if ($timestamp !== false) {
            (new \PragmaRX\Google2FALaravel\Google2FA($request))->login();

            $user->google2fa_secret = $secret;
            $user->google2fa_ts = $timestamp;
            $user->save();
            return $request->secret ? back() : redirect()->route('user.dashboard');
        }

        throw ValidationException::withMessages(['one_time_password' => [
            'required' => 'Invalid one time password'
        ]]);
    }


    public function destroy()
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $user->disableTwoFactorAuthentication();
        session()->forget('google2fa_secret');
        return back();
    }
}
