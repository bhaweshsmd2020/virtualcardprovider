<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminMiddleware
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
        if (Auth::check() && Auth::user()->role == 'admin' &&  Auth::user()->status == 1) {
           
            return $next($request);
        } elseif (Auth::check() && Auth::user()->status != 1) {
            Auth::logout();
            Toastr::danger(__('Your Account Is Deactivated'));
        }

        return redirect('/login');
    }
}
