<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\LoginDetail;
use Illuminate\Support\Carbon;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
            'authPages' => get_option('auth_pages', true)
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => $request->password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
            $user = User::query()->where('email', $request->email)->first();
            auth()->login($user);

            $ipAddress = $request->ip();
            // $ipAddress = '122.161.52.75';
            $url = "http://ip-api.com/json/{$ipAddress}";
            $response = file_get_contents($url);
            $locationData = json_decode($response, true);

            LoginDetail::where('user_id', auth()->user()->id)->update(['status' => 0]);

            LoginDetail::create([
                'user_id' => auth()->user()->id, 
                'ip_address' => $ipAddress, 
                'country' => $locationData['country'], 
                'country_code' => $locationData['countryCode'], 
                'region_name' => $locationData['regionName'], 
                'city' => $locationData['city'], 
                'zip' => $locationData['zip'], 
                'lat' => $locationData['lat'], 
                'lon' => $locationData['lon'], 
                'timezone' => $locationData['timezone'], 
                'isp' => $locationData['isp'], 
                'login_at' => Carbon::now(),
            ]);
            
            return back();
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
