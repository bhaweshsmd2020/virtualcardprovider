<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\VirtualCurrency;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirectTo($provider)
    {
        session(['user_role' => request()->r]);
        return Socialite::driver($provider)->redirect();
    }

    public function handleCallback($provider)
    {

        try {
            $user = Socialite::driver($provider)->user();

            $findUser = User::where('provider_id', $user->id)
                ->where('provider', $provider)
                ->where('email', $user->email)->first();

            $uuid = (string) Str::uuid();

            if ($findUser) {
                $findUser->update(['password' => Hash::make($uuid . $user->id)]);
                Auth::login($findUser);
                return redirect('/user/dashboard');
            } else {

                $plan = null;
                if (session('plan_id')) {
                    $plan = Plan::query()->where('status', 1)->find(session('plan_id'));
                } else {
                    $plan = Plan::query()->where('status', 1)->where('is_default', 1)->first();
                }

                DB::transaction(function () use ($provider, $user, $plan) {

                    $willExpire = null;
                    $willExpire = $plan && $plan->is_trial ? now()->addDays($plan->trial_days) : now()->addDays($plan->days);

                    $newUser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'provider_id' => $user->id,
                        'provider' => $provider,
                        'plan_data' => $plan->data ?? null,
                        'plan_id' => $plan->plan_id ?? null,
                        'will_expire' => $willExpire,
                        'password' => Hash::make(Str::uuid() . $user->id),
                        'email_verified_at' => now(),
                        'role' => 'user',
                    ]);

                    $defaultCurrency = VirtualCurrency::query()
                        ->where('is_default', 1)
                        ->firstOrFail();

                    $newUser->wallets()->create([
                        'virtual_currency_id' => $defaultCurrency->id,
                        'balance' => 0,
                    ]);

                    Auth::login($newUser);

                });

                if (auth()->user()->will_expire === null && auth()->user()->plan_id !== null) {
                    return inertia_location(route('user.subscription.payment', auth()->user()->plan_id));
                }

                return inertia_location('/account/setup');
            }

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
