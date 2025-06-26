<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Toastr;
use Illuminate\Http\Request;

class SaasMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User */
        $user = $request->user();

        $inAllowedRoutes = [
            'user/dashboard*',
            // 'user/account*',
            'user/subscription*',
            'user/support*',
            'user/kyc*',
            'user/profile*',
            'user/account-settings*',
        ];
        if ($request->is($inAllowedRoutes)) {
            return $next($request);
        }

        if (!$user->plan_data && !$request->is($inAllowedRoutes)) {
            return redirect('/user/dashboard')->with('flash', ['type' => 'error', 'message' => __("You haven't purchased any subscription plans yet.")]);
        }

        if ($user->will_expire == null && !$request->is($inAllowedRoutes)) {
            Toastr::setToast('danger', __('Your subscription payment is not completed'));
            $redirect_url = $user->plan_id == null ? '/user/subscription' : '/user/subscription/payment/' . $user->plan_id;
            return redirect($redirect_url);
        }

        if ($user->will_expire < now() && !$request->is($inAllowedRoutes)) {
            Toastr::setToast('danger', __('Your subscription payment was expired please renew the subscription'));
            return redirect('/user/dashboard');
        }

        return $next($request);
    }
}
