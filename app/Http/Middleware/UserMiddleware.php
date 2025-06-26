<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
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
        if ($request->user()->role == 'user' && $request->user()->status == 1) {
            return $next($request);
        }
        elseif($request->user()->status != 1){
            Auth::logout();
            Session::flash('error', __('Your Account Has Disabled By Admin Please Contact Us'));
            return redirect('/login');
        }
        return redirect(route($request->user()->getDashboardRoute()));
    }
}
