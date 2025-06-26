<?php

namespace App\Http\Controllers\Admin;

use DateTimeZone;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\Dotenv;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\Option;
use App\Models\VirtualCurrency;
use App\Models\Setting;
use App\Traits\Uploader;
use App\Models\EmailConfig;
use App\Helpers\EmailHelper;

class DeveloperSettingsController extends Controller
{
    use Dotenv, Uploader;

    public function __construct()
    {
        $this->middleware('permission:developer-settings');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): Response
    {
        $segments = request()->segments();

        $buttons = [
            [
                'name' => __('Back to dashboard'),
                'url' => url('/admin/dashboard')
            ],
        ];

        if ($id == 'app-settings') {
            $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
            $languages = get_option('languages');
            $appName      = env('APP_NAME');
            $appDebug     = env('APP_DEBUG');
            $timeZone     = env('TIME_ZONE', 'UTC');
            $default_lang = env('DEFAULT_LANG', 'en');

            return Inertia::render('Admin/Developer/App', compact('id', 'tzlist', 'languages', 'appName', 'appDebug', 'timeZone', 'default_lang', 'segments', 'buttons'));
        } elseif ($id == 'card-settings') {
            $base_url = Setting::where('name', 'base_url')->first()->value;
            $client_id = Setting::where('name', 'client_id')->first()->value;
            $client_secret = Setting::where('name', 'client_secret')->first()->value;
            $redirect_url = Setting::where('name', 'redirect_url')->first()->value;
            $interval = Setting::where('name', 'interval')->first()->value;
            return Inertia::render('Admin/Developer/Card', compact('id', 'base_url', 'client_id', 'client_secret', 'redirect_url', 'interval'));
        } elseif ($id == 'mail-settings') {          
            $eamil_config = EmailConfig::where('id', '1')->first();

            $STATUS           = $eamil_config->status;
            $MAIL_DRIVER      = $eamil_config->email_protocol;
            $MAIL_HOST        = $eamil_config->smtp_host;
            $MAIL_PORT        = $eamil_config->smtp_port;
            $MAIL_USERNAME    = $eamil_config->smtp_username;
            $MAIL_PASSWORD    = $eamil_config->smtp_password;
            $MAIL_ENCRYPTION  = $eamil_config->email_encryption;
            $MAIL_FROM_ADDRESS = $eamil_config->from_address;
            $MAIL_FROM_NAME   = $eamil_config->from_name;
            $MAIL_TO          = $eamil_config->notification_email;

            $emailHelper = new EmailHelper();
            $verify = $emailHelper->sendEmail('bhawesh.smd@gmail.com', 'Email Config', 'Email Config updated');

            return Inertia::render('Admin/Developer/Smtp', compact(
                'id',
                'segments',
                'buttons',
                'STATUS',
                'MAIL_DRIVER',
                'MAIL_HOST',
                'MAIL_PORT',
                'MAIL_USERNAME',
                'MAIL_PASSWORD',
                'MAIL_ENCRYPTION',
                'MAIL_FROM_ADDRESS',
                'MAIL_FROM_NAME',
                'MAIL_TO',
            ));
        } elseif ($id == 'newsletter-settings') {

            $NEWSLETTER_API_KEY = env('NEWSLETTER_API_KEY');
            $NEWSLETTER_LIST_ID = env('NEWSLETTER_LIST_ID');

            return Inertia::render('Admin/Developer/Newsletter', compact(
                'id',
                'NEWSLETTER_API_KEY',
                'NEWSLETTER_LIST_ID',
                'segments',
                'buttons'
            ));
        } elseif ($id == 'storage-settings') {
            $FILESYSTEM_DISK = env('FILESYSTEM_DISK');
            $WAS_ACCESS_KEY_ID = env('WAS_ACCESS_KEY_ID');
            $SECRET_ACCESS_KEY = env('SECRET_ACCESS_KEY');
            $WAS_DEFAULT_REGION = env('WAS_DEFAULT_REGION');
            $WAS_BUCKET = env('WAS_BUCKET');
            $WAS_ENDPOINT = env('WAS_ENDPOINT');

            return Inertia::render('Admin/Developer/Storage', compact(
                'id',
                'FILESYSTEM_DISK',
                'WAS_ACCESS_KEY_ID',
                'SECRET_ACCESS_KEY',
                'WAS_DEFAULT_REGION',
                'WAS_BUCKET',
                'WAS_ENDPOINT',
                'segments',
                'buttons'
            ));
        } elseif ($id == 'stripe-settings') {
            $STRIPE_API_KEY = env('STRIPE_API_KEY');
            $STRIPE_PUBLIC_API_KEY = env('STRIPE_PUBLIC_API_KEY');
            $STRIPE_CURRENCY = env('STRIPE_CURRENCY');
            $creditCardCurrencies = ['usd', 'eur', 'gbp'];
            return Inertia::render('Admin/Developer/Stripe', compact(
                'id',
                'STRIPE_API_KEY',
                'STRIPE_PUBLIC_API_KEY',
                'STRIPE_CURRENCY',
                'creditCardCurrencies',
            ));
        } elseif ($id == 'twilio-settings') {
            $TWILIO_ACCOUNT_SID = env('TWILIO_ACCOUNT_SID');
            $TWILIO_AUTH_TOKEN = env('TWILIO_AUTH_TOKEN');
            $TWILIO_NUMBER = env('TWILIO_NUMBER');
            return Inertia::render('Admin/Developer/Twilio', compact(
                'id',
                'TWILIO_ACCOUNT_SID',
                'TWILIO_AUTH_TOKEN',
                'TWILIO_NUMBER',
            ));
        } elseif ($id == 'features-settings') {
            $EMAIL_VERIFICATION = env('EMAIL_VERIFICATION');
            $PHONE_VERIFICATION = env('PHONE_VERIFICATION');
            $KYC_VERIFICATION = env('KYC_VERIFICATION');
            return Inertia::render('Admin/Developer/Features', compact(
                'id',
                'EMAIL_VERIFICATION',
                'PHONE_VERIFICATION',
                'KYC_VERIFICATION',
            ));
        } elseif ($id == 'currency-settings') {
            $virtualCurrency = VirtualCurrency::firstWhere('is_default', 1);
            $rates = ExchangeRate::query()
                ->where('virtual_currency_id', $virtualCurrency->id)
                ->with('exchange_currency:id,currency')
                ->get();
            $currencies = VirtualCurrency::where('id', '!=', $virtualCurrency->id)
                ->get()
                ->map(function ($currency) {
                    return [
                        'id' => $currency->id,
                        'currency' => $currency->currency,
                    ];
                });
            $base_currency = get_option('base_currency');
            return Inertia::render('Admin/Developer/Currency', compact(
                'id',
                'virtualCurrency',
                'rates',
                'currencies',
                'base_currency',
            ));
        } elseif ($id == 'social-login-settings') {

            $GOOGLE_CLIENT_ID = env('GOOGLE_CLIENT_ID');
            $GOOGLE_CLIENT_SECRET = env('GOOGLE_CLIENT_SECRET');
            $GOOGLE_REDIRECT_URL = env('GOOGLE_REDIRECT_URL');

            $FACEBOOK_CLIENT_ID = env('FACEBOOK_CLIENT_ID');
            $FACEBOOK_CLIENT_SECRET = env('FACEBOOK_CLIENT_SECRET');
            $FACEBOOK_REDIRECT_URL = env('FACEBOOK_REDIRECT_URL');

            return Inertia::render('Admin/Developer/SocialLogin', compact(
                'id',
                'GOOGLE_CLIENT_ID',
                'GOOGLE_CLIENT_SECRET',
                'GOOGLE_REDIRECT_URL',
                'FACEBOOK_CLIENT_ID',
                'FACEBOOK_CLIENT_SECRET',
                'FACEBOOK_REDIRECT_URL',
                'segments',
                'buttons'
            ));
        } elseif ($id == 'cookie-settings') {

            $COOKIE_CONSENT = env('COOKIE_CONSENT');
            $cookieData = json_decode(file_get_contents(database_path('json/cookie_data.json')), true);
            return Inertia::render('Admin/Developer/Cookie', compact(
                'id',
                'COOKIE_CONSENT',
                'cookieData',
                'segments',
                'buttons'
            ));
        }

        abort(404);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($id == 'app-settings') {
            $this->editEnv('APP_NAME', Str::slug($request->APP_NAME));
            $this->editEnv('APP_DEBUG', filter_var($request->APP_DEBUG, FILTER_VALIDATE_BOOLEAN), true);
            $this->editEnv('TIME_ZONE', $request->TIME_ZONE);
            $this->editEnv('DEFAULT_LANG', $request->DEFAULT_LANG ?? 'en');
        } elseif ($id == 'card-settings') {            
            $base_url = Setting::where('name', 'base_url')->update(['value' => $request->base_url]);
            $client_id = Setting::where('name', 'client_id')->update(['value' => $request->client_id]);
            $client_secret = Setting::where('name', 'client_secret')->update(['value' => $request->client_secret]);
            $redirect_url = Setting::where('name', 'redirect_url')->update(['value' => $request->redirect_url]);
            $interval = Setting::where('name', 'interval')->update(['value' => $request->interval]);
        } elseif ($id == 'storage-settings') {
            $this->editEnv('FILESYSTEM_DISK', $request->FILESYSTEM_DISK);
            $this->editEnv('WAS_ACCESS_KEY_ID', $request->WAS_ACCESS_KEY_ID);
            $this->editEnv('SECRET_ACCESS_KEY', $request->SECRET_ACCESS_KEY);
            $this->editEnv('WAS_DEFAULT_REGION', $request->WAS_DEFAULT_REGION);
            $this->editEnv('WAS_BUCKET', $request->WAS_BUCKET);
            $this->editEnv('WAS_ENDPOINT', $request->WAS_ENDPOINT);
        } elseif ($id == 'newsletter-settings') {
            $this->editEnv('NEWSLETTER_API_KEY', $request->NEWSLETTER_API_KEY);
            $this->editEnv('NEWSLETTER_LIST_ID', $request->NEWSLETTER_LIST_ID);
        } elseif ($id == 'mail-settings') {
            EmailConfig::where('id', '1')->update([
                'email_protocol' => $request->MAIL_DRIVER, 
                'email_encryption' => $request->MAIL_ENCRYPTION, 
                'smtp_host' => $request->MAIL_HOST, 
                'smtp_port' => $request->MAIL_PORT,  
                'smtp_username' => $request->MAIL_USERNAME, 
                'smtp_password' => $request->MAIL_PASSWORD, 
                'from_address' => $request->MAIL_FROM_ADDRESS, 
                'from_name' => $request->MAIL_FROM_NAME, 
                'status' => $request->STATUS, 
                'notification_email' => $request->MAIL_TO
            ]);

            
        } elseif ($id == 'stripe-settings') {
            $this->editEnv('STRIPE_API_KEY', $request->STRIPE_API_KEY);
            $this->editEnv('STRIPE_PUBLIC_API_KEY', $request->STRIPE_PUBLIC_API_KEY);
            $this->editEnv('STRIPE_CURRENCY', $request->STRIPE_CURRENCY);
        } elseif ($id == 'twilio-settings') {
            $this->editEnv('PHONE_VERIFICATION', $request->PHONE_VERIFICATION, true);
            $this->editEnv('TWILIO_ACCOUNT_SID', $request->TWILIO_ACCOUNT_SID);
            $this->editEnv('TWILIO_AUTH_TOKEN', $request->TWILIO_AUTH_TOKEN);
            $this->editEnv('TWILIO_NUMBER', $request->TWILIO_NUMBER);
        } elseif ($id == 'features-settings') {
            $this->editEnv('EMAIL_VERIFICATION', $request->EMAIL_VERIFICATION, true);
            $this->editEnv('PHONE_VERIFICATION', $request->PHONE_VERIFICATION, true);
            $this->editEnv('KYC_VERIFICATION', $request->KYC_VERIFICATION, true);
        } elseif ($id == 'currency-settings') {
            $request->validate([
                'preview' => request()->hasFile('preview') ? 'image' : 'nullable|string',
                'currency' => 'required|string',
                'precision' => 'required|integer|min:2',
            ]);
            $virtualCurrency = VirtualCurrency::where('id', $request->virtual_currency_id)
                ->where('is_default', 1)->firstOrFail();

            if ($request->hasFile('preview')) {
                $preview = $this->uploadFile('preview');
            }
            $virtualCurrency->update([
                'currency' => str()->lower($request->currency),
                'is_default' => 1,
                'precision' => $request->precision,
                'preview' => $preview ?? $virtualCurrency->preview
            ]);
            $option = Option::firstOrNew(['key' => 'base_currency']);
            $optionValue = [
                'name' => $virtualCurrency->currency,
                'icon' => $request->icon,
                'position' => $request->position
            ];
            $option->value = $optionValue;
            $option->save();
            if (request()->filled('rates')) {
                $request->validate(['rates' => 'array']);
                foreach (request()->rates as $rate) {
                    $virtualCurrency->rates()->updateOrCreate([
                        'virtual_currency_exchange_id' => $rate['id'],
                    ], ['rate' => $rate['rate']]);
                }
            }
        } elseif ($id == 'cookie-settings') {
            $this->editEnv('COOKIE_CONSENT', $request->COOKIE_CONSENT, true);
            file_put_contents(database_path('json/cookie_data.json'), json_encode($request->cookieData));
        }

        return back();
    }
}
