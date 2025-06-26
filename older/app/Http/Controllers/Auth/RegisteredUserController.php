<?php

namespace App\Http\Controllers\Auth;

use App\Models\Plan;
use App\Models\User;
use Inertia\Inertia;
use App\Helpers\SeoMeta;
use App\Helpers\Toastr;
use Illuminate\Http\Request;
use App\Models\VirtualCurrency;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterUserRequest;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class RegisteredUserController extends Controller
{
    public function create(Request $request)
    {
        if (isset($request->id)) {
            $plan = Plan::query()->where('status', 1)
                ->where('id', request()->id)->firstOrFail();
            session(['plan_id' => $plan->id]);
            return to_route('register');
        }

        SeoMeta::init('seo_register');

        return Inertia::render('Auth/Register', [
            'authPages' => get_option('auth_pages', true),
        ]);
    }    

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'user',
                'acceptterms' => $request->acceptterms
            ]);
            $defaultCurrency = VirtualCurrency::query()
                ->where('is_default', 1)
                ->firstOrFail();

            $user->wallets()->create([
                'virtual_currency_id' => $defaultCurrency->id,
                'balance' => 0,
            ]);
            if (session('plan_id') && $plan = Plan::query()->where('status', 1)->find(session('plan_id'))) {
                $user->plan_data = $plan->data;
                $user->plan_id = $plan->id;
                $user->will_expire = $plan->is_trial == 1 ? now()->addDays($plan->trial_days) : null;
                $user->save();

                Auth::login($user);

                if ($user->will_expire === null) {
                    return inertia_location(route('user.subscription.payment', $plan->id));
                }
            } else {
                $plan = Plan::query()->where('status', 1)->where('is_default', 1)->first();
                if (!empty ($plan)) {
                    $user->plan_data = $plan->data;
                    $user->plan_id = $plan->id;
                    $user->will_expire = now()->addDays($plan->days);
                    $user->save();
                }
            }
            Auth::login($user);
        });

        if (env('EMAIL_VERIFICATION', false)) {

            $user = User::where('id', auth()->user()->id)->first();
            try {               

                $verificationUrl = $this->verificationUrl($user);                

                $template = EmailTemplate::where(['id' => '1', 'type' => 'email'])->select('subject', 'body')->first();

                $template_sub = $template->subject;
                $template_msg = str_replace('{user}', 'User', $template->body);
                $template_msg = str_replace('{username}', $user->email, $template_msg);
                $template_msg = str_replace('{activate_url}', $verificationUrl, $template_msg);

                $emailHelper = new EmailHelper();
                $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

            } catch (\Throwable $th) {
                Toastr::error($th->getMessage() ?? __('Something went wrong while sending verification email'));                
            }

            return to_route('verification.notice');
        }

        return to_route('user.dashboard');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
