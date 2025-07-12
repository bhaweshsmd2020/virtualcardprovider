<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Toastr;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;

class VerifyOtpController extends Controller
{
    public function index(Request $request)
    {
        // forget the session if expired
        if (!session('otp_info') || (session('otp_info.expires_at') && Carbon::parse(session('otp_info.expires_at'))->isPast())) {
            session()->forget('otp_info');
            return to_route('phone.verification.index');
        }


        $otpInfo = collect(session('otp_info'))
            ->merge([
                'remaining' => Carbon::parse(session('otp_info.expires_at'))->diffInSeconds(now(), true)
            ])
            ->except(['code'])
            ->toArray();

        return Inertia::render('Auth/VerifyOtp', [
            'status' => 'otp-sent',
            'authPages' => get_option('auth_pages', true),
            'otpInfo' => $otpInfo
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'numeric', 'min_digits:4'],
        ]);

        // check session has otp info and otp is not expired
        if (!session('otp_info') || (session('otp_info.expires_at') && Carbon::parse(session('otp_info.expires_at'))->isPast())) {
            session()->forget('otp_info');
    
            Session::flash('flash', [
                'type' => 'error',
                'message' => __('The OTP you entered is expired'),
            ]);
    
            return Inertia::location(route('phone.verification.index'));
        }

        //   verify the otp code with request otp code
        if ($request->otp_code != session('otp_info.code')) {
            return back()->with('flash', ['type' => 'error', 'message' => __('The OTP you entered is incorrect')]);
        }

        $request->user()->update([
            'phone' => session('otp_info.phone'),
            'phone_verified_at' => now()
        ]);

        session()->forget('otp_info');

        $template = EmailTemplate::where(['id' => '2', 'type' => 'email'])->select('subject', 'body')->first();

        $user = $request->user();

        $template_sub = $template->subject;
        $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
        $template_msg = str_replace('{dashboard}', route('admin.dashboard'), $template_msg);

        $emailHelper = new EmailHelper();
        $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

        return inertia_location(route('user.dashboard'));
    }

    public function verifyFirebaseOtp(Request $request)
    {
        try {
            $firebase = (new Factory)->withServiceAccount(base_path('firebase_credentials.json'));
            $auth = $firebase->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($request->input('idToken'));

            $uid = $verifiedIdToken->claims()->get('sub');
            $phone = $verifiedIdToken->claims()->get('phone_number');

            // Find or create user by phone
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => 'Firebase User',
                    'firebase_uid' => $uid,
                    'phone_verified_at' => now(),
                    'password' => bcrypt(str()->random(16))
                ]
            );

            // Update firebase UID and phone verification if needed
            if (!$user->firebase_uid) {
                $user->update([
                    'firebase_uid' => $uid,
                    'phone_verified_at' => now(),
                ]);
            }

            // Generate Laravel token (e.g., Sanctum or Passport)
            $token = $user->createToken('firebase-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Firebase token',
                'exception' => $e->getMessage()
            ], 401);
        }
    }
}
