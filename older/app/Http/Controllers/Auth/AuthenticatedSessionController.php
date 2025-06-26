<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\SeoMeta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;
use App\Models\LoginDetail;
use Illuminate\Support\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        SeoMeta::init('seo_login');
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'authPages' => get_option('auth_pages', true)
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {       
        $request->authenticate();

        if (auth()->user()->deleted_at) {
            auth()->logout();
            throw ValidationException::withMessages([
                'email' => trans('This account has been temporary inactive, please contact with admin to get back'),
            ]);
        }

        $request->session()->regenerate();
        if(auth()->user()->role == 'admin'){
            return inertia()->location(route('admin.dashboard'));
        }

        $ipAddress = $request->ip();
        // $ipAddress = '122.161.52.75';
        $url = "http://ip-api.com/json/{$ipAddress}";
        $response = file_get_contents($url);
        $locationData = json_decode($response, true);

        LoginDetail::where('user_id', auth()->user()->id)->update(['status' => 0]);

        LoginDetail::create([
            'user_id' => auth()->user()->id, 
            'ip_address' => $ipAddress??'', 
            'country' => $locationData['country']??'', 
            'country_code' => $locationData['countryCode']??'', 
            'region_name' => $locationData['regionName']??'', 
            'city' => $locationData['city']??'', 
            'zip' => $locationData['zip']??'', 
            'lat' => $locationData['lat']??'', 
            'lon' => $locationData['lon']??'', 
            'timezone' => $locationData['timezone']??'', 
            'isp' => $locationData['isp']??'', 
            'login_at' => Carbon::now(),
        ]);
        return inertia()->location(route('user.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return inertia()->location('/');
    }
}
