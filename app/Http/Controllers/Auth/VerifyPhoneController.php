<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Toastr;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PhoneVerificationRequest;
use App\Models\User;
use App\Models\Country;

class VerifyPhoneController extends Controller
{
    public function index(Request $request)
    {
        if (config('feature.phone_verification') == false || $request->user()->hasVerifiedPhone()) {
            return $this->redirectToUserDashboard();
        }

        // if (auth()->user()->phone && $this->otpAlreadySent()) {
        //     return to_route('otp.verification.index');
        // }

        // get the dialers from resource json country list file
        $countries = Country::active()->get();
        $codes = json_decode(file_get_contents(resource_path('json/country_codes.json')), true);

        $userInfo = [];
        if (auth()->user()->phone) {
            $userInfo = [
                'country_code' => auth()->user()->country_code,
                'phone' => auth()->user()->phone,
            ];
        }

        return Inertia::render('Auth/VerifyPhone', [
            'countries' => $countries,
            'codes' => $codes,
            'authPages' => get_option('auth_pages', true),
            'userInfo' => $userInfo,
            'status' => session('status'),
        ]);
    }

    public function store(PhoneVerificationRequest $request)
    {
        if (config('feature.phone_verification') == false || $request->user()->hasVerifiedPhone()) {
            return $this->redirectToUserDashboard();
        }

        // if ($this->otpAlreadySent()) {
        //     return to_route('otp.verification.index');
        // }

        $request->validate([
            'country_code' => ['required'],
            'phone' => ['required', 'min:9', 'max:14'],
        ]);

        $otpCode = random_int(100000, 999999);
        $message = __('Your Verification code for Virtual Card Provider : '.$otpCode);
        $to = $request->country_code . $request->phone;

        $twilioClient = new TwilioService();
        $sendOtpRes = $twilioClient->sendMessage($to, $message);

        if ($sendOtpRes->failed()) {
            return back()->with('flash', ['type' => 'error', 'message' => __('Something went wrong, please try again.')]);
        }

        User::where('id', auth()->user()->id)->update([
            'country_code' => $request->country_code,
            'phone' => $request->phone,
        ]);

        $expiresAt = now()->addMinutes(2);

        session()->put('otp_info', [
            'code' => $otpCode,
            'country_code' => $request->country_code,
            'phone' => $request->phone,
            'expires_at' => $expiresAt,
        ]);

        return to_route('otp.verification.index', ['expires_at' => $expiresAt->toDateTimeString()])
       ->with('flash', ['type' => 'success', 'message' => __('6 - digit OTP has been sent successfully on your registered phone number')]);

    }

    public function otpAlreadySent(): bool
    {
        return session()->has('otp_info');
    }

    public function redirectToUserDashboard(): \Symfony\Component\HttpFoundation\Response
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        session()->forget('otp_info');

        return Inertia::location(route('user.dashboard'));
    }
}
