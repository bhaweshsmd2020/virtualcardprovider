<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use App\Helpers\Toastr;
use App\Models\Country;
use App\Helpers\SeoMeta;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationSetupRequest;
use App\Models\OtherState;
use App\Models\OtherCity;

class AccountSetupController extends Controller
{
    public function index()
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        $isAccountSetupCompleted = (
            $user->name &&
            $user->dob_day &&
            $user->dob_month &&
            $user->dob_year &&
            $user->country_id &&
            $user->state_id &&
            $user->city_id &&
            $user->address_line &&
            $user->phone
        );
        if ($isAccountSetupCompleted) {
            return to_route('user.dashboard');
        }

        SeoMeta::init('account_setup');
        $authPages = get_option('auth_pages', true);

        $countries = Country::active()->get();
        $codes =  json_decode(file_get_contents(resource_path('json/country_codes.json')), true);
        return Inertia::render('Auth/SetLocation', compact('countries', 'authPages', 'codes'));
    }

    public function store(StoreLocationSetupRequest $request)
    {
        $user = auth()->user();
        $user->update($request->validated());
        //Toastr::success(__('Information has been saved successfully'));

        // send otp if phone verification is disabled
        if (config('feature.phone_verification') == false) {
            return Inertia::location(route('user.dashboard'));
        }

        if($request->state_id == '1000'){
            OtherState::create([
                'user_id' => $user->id,
                'state_id' => $request->state_id,
                'name' => $request->other_state,
            ]);
        }

        if($request->state_id == '1000'){
            OtherCity::create([
                'user_id' => $user->id,
                'city_id' => $request->state_id,
                'name' => $request->other_city,
            ]);
        }        

        // otp verification is enabled now send otp
        $otpCode = random_int(100000, 999999);
        $message = __('Your Verification code for Virtual Card Provider : '.$otpCode);
        $to = $user->country_code . $user->phone;

        $twilioClient = new TwilioService();
        $sendOtpRes = $twilioClient->sendMessage($to, $message);

        $expiresAt = now()->addMinutes(2);

        if ($sendOtpRes->successful()) {
            session()->put('otp_info', [
                'code' => $otpCode,
                'country_code' =>  $request->country_code,
                'phone' => $request->phone,
                'expires_at' => $expiresAt,
            ]);

            return to_route('otp.verification.index', ['expires_at' => $expiresAt->toDateTimeString()])
            ->with('flash', ['type' => 'success', 'message' => __('6 - digit OTP has been sent successfully on your registered phone number')]);
        }

        return to_route('phone.verification.index')->with('flash', ['type' => 'error', 'message' => __('Something went wrong, please try again.')]);
    }
}
