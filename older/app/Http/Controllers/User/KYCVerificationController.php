<?php

namespace App\Http\Controllers\User;

use App\Helpers\Toastr;
use App\Helpers\CardHelper;
use App\Http\Controllers\Controller;
use App\Models\KycMethod;
use App\Models\KYCRequest;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Inertia\Inertia;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\EmailConfig;

class KYCVerificationController extends Controller
{
    public function index()
    {
        $KYCDocuments = KYCRequest::whereUserId(Auth::id())->with('method')->latest()->paginate();

        return Inertia::render('User/KYC/Index', compact('KYCDocuments'));
    }

    public function create($id)
    {
        abort_if(Auth::user()->kyc_verified_at, 403);

        $type = KycMethod::where('id', $id)->first();
        if($type->type == 1){
            $kyc_type = 'identity';
        }else{
            $kyc_type = 'address';
        }

        $kyc_methods = KycMethod::where('id', $id)->whereStatus(1)->get();
        $KYCDocuments = KYCRequest::whereUserId(Auth::id())->where('type', $kyc_type)->first();
        if(!empty($KYCDocuments)){
            if($KYCDocuments->status == 2){
                return Inertia::render('User/KYC/Create', compact('kyc_methods', 'kyc_type'));
            }else{
                return to_route('user.kyc.index');
            }            
        }        

        return Inertia::render('User/KYC/Create', compact('kyc_methods', 'kyc_type'));
    }

    public function store(Request $request)
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        if (config('feature.kyc_verification') == false) {
            Toastr::warning('This feature is currently disabled');
            return back();
        }

        if ($request->user()->hasVerifiedKYC()) {
            Toastr::warning('You\'re already verified');
            return back();
        }

        $request->validate([
            'method' => ['required'],
        ]);

        $method = KycMethod::findOrFail($request->input('method'));

        $data = [];
        foreach ($request->fields as $item) {

            Validator::make(
                $item,
                [
                    'value' => ['required'],
                ],
                [],
                [
                    'value' => $item['label'],
                ]
            )->validate();

            if ($item['type'] == 'file') {
                Validator::make(
                    $item,
                    [
                        'value' => ['required', File::types(['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'])->max(5000)],
                    ],
                    [],
                    [
                        'value' => $item['label'],
                    ]
                )->validate();
            }

            $value = $item['value'] ?? '';

            if ($value && is_file($value)) {
                $file = $value;
                $path = '/uploads/' . date('y/m');
                $filename = $item['label'] . '.' . $file->extension();
                $filepath = $path . '/' . $filename;
                Storage::put($filepath, file_get_contents($value));
                $item['value'] = url($filepath);
            }
            $data[] = $item;
        }

        DB::transaction(function () use ($request, $method, $data) {               
            
            $check = KYCRequest::where('user_id', Auth::id())->where('type', $request->input('type'))->first();
            if(!empty($check)){
                $kyc_request = KYCRequest::where('user_id', Auth::id())->where('type', $request->input('type'))->update([
                    'status' => 3,
                    'note' => $request->input('note'),
                    'data' => $data,
                    'fields' => $method->fields,
                ]);

                if($request->input('type') == 'identity'){
                    $message = 'Identity KYC verification resubmit';
                }else{
                    $message = 'Address KYC verification resubmit';
                }

                Notification::create([
                    'user_id' => auth()->id(),
                    'title' => $message,
                    'url' => route('admin.kyc-requests.show', $check->id),
                    'comment' => auth()->user()->name . __(' resend a kyc verification request'),
                    'for_admin' => 1,
                ]);
                
            }else{           
                $kyc_request = KYCRequest::create([
                    'user_id' => Auth::id(),
                    'kyc_method_id' => $request->input('method'),
                    'status' => 0,
                    'note' => $request->input('note'),
                    'data' => $data,
                    'fields' => $method->fields,
                    "type" => $request->input('type')
                ]);

                $method->users()->attach(Auth::id(), ['kyc_request_id' => $kyc_request->id]);            

                $kycRequests = KYCRequest::where('user_id', Auth::id())->get();

                $hasIdentity = false;
                $hasAddress = false;

                foreach ($kycRequests as $kycRequest) {
                    if ($kycRequest->type === 'identity') {
                        $hasIdentity = true;
                    }
                    if ($kycRequest->type === 'address') {
                        $hasAddress = true;
                    }
                }

                $isKYCComplete = $hasIdentity && $hasAddress;

                if ($isKYCComplete) {

                    if(empty(auth()->user()->card_user_id)){
                        $cardHelper = new CardHelper();
                        $checkEmail = $cardHelper->createUser();
                        if($checkEmail == 'invalid'){
                            return back();
                        }
                    }              
                    
                    $emailConfig = EmailConfig::where(['email_protocol' => 'smtp', 'status' => '1'])->first();
                    $template = EmailTemplate::where(['id' => '7', 'type' => 'email'])->select('subject', 'body')->first();

                    $user = auth()->user();

                    $template_sub = $template->subject;
                    $template_msg = str_replace('{name}', $user->first_name . ' ' . $user->last_name, $template->body);
                    $template_msg = str_replace('{date}', now(), $template_msg); 

                    $emailHelper = new EmailHelper();
                    $verify = $emailHelper->sendEmail($emailConfig->notification_email, $template_sub, $template_msg);
                }  
                
                if($request->input('type') == 'identity'){
                    $message = 'New identity KYC verification request';
                }else{
                    $message = 'New address KYC verification request';
                }

                Notification::create([
                    'user_id' => auth()->id(),
                    'url' => route('admin.kyc-requests.show', $kyc_request),
                    'title' => $message,
                    'comment' => auth()->user()->name . __('Send a kyc verification requests'),
                    'for_admin' => 1,
                ]);
            }
        });

        //Toastr::success(__('Your account is being verified, please allow us 1 working day to review your account! Need more help . Get in touch via support@lubypay.com.'));

        return to_route('user.kyc.index');
    }

    public function show(KYCRequest $kyc)
    {
        return Inertia::render('User/KYC/Show', compact('kyc'));
    }

    public function resubmit(KYCRequest $kyc)
    {
        abort_if(Auth::user()->kyc_verified_at, 403);
        abort_if($kyc->status !== 2, 403);
        return Inertia::render('User/KYC/ReSubmit', compact('kyc'));
    }

    public function resubmitUpdate(Request $request, KYCRequest $kyc)
    {
        /**
         * @var \App\Models\User
         */

        abort_if($kyc->status !== 2, 403, __('You\'re already submitted'));

        $request->validate([
            'note' => ['nullable', 'string'],
            'fields' => ['required', 'array'],
        ]);

        $data = [];
        foreach ($request->fields as $item) {
            if ($item['type'] == 'file') {
                Validator::make(
                    $item,
                    [
                        'value' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
                    ],
                    [],
                    [
                        'value' => $item['label'],
                    ]
                )->validate();
            }

            $value = $item['value'];

            if ($value && is_file($value) && $item['type'] == 'file') {
                $file = $value;
                $path = '/uploads/' . date('y/m');
                $filename = $item['label'] . '.' . $file->extension();
                $filepath = $path . '/' . $filename;
                Storage::put($filepath, file_get_contents($value));
                $item['value'] = $filepath;
            }
            $data[] = $item;
        }

        DB::transaction(function () use ($request, $data, $kyc) {

            $kyc->update([
                'user_id' => Auth::id(),
                'status' => 3,
                'note' => $request->input('note'),
                'fields' => $data,
            ]);

            Notification::create([
                'user_id' => auth()->id(),
                'title' => __('KYC verification Resubmit'),
                'url' => url('admin/kyc-requests/' . $kyc->id),
                'comment' => auth()->user()->name . __(' resend a kyc verification request'),
                'for_admin' => 1,
            ]);
        });

        //Toastr::success(__('Your account is being verified, please allow us  1 working day to review your account! Need more help . Get in touch via support@lubypay.com.'));

        return to_route('user.kyc.index');
    }

    public function tips($type)
    {
        $KYCDocuments = KYCRequest::whereUserId(Auth::id())->where('type', $type)->first();
        if(!empty($KYCDocuments)){
            if($KYCDocuments->status == 2){
                return Inertia::render('User/KYC/Tips', compact('type'));
            }else{
                return to_route('user.kyc.index');
            }            
        }
        return Inertia::render('User/KYC/Tips', compact('type'));
    }
    
    public function types($type)
    {
        if($type == 'identity'){
            $kyc_type = '1';
        }else{
            $kyc_type = '0';
        }

        $kyc_methods = KycMethod::whereStatus(1)->where('type', $kyc_type)->get();
        $KYCDocuments = KYCRequest::whereUserId(Auth::id())->where('type', $type)->first();
        if(!empty($KYCDocuments)){
            if($KYCDocuments->status == 2){
                return Inertia::render('User/KYC/Type', compact('kyc_methods', 'type'));
            }else{
                return to_route('user.kyc.index');
            }            
        }
        return Inertia::render('User/KYC/Type', compact('kyc_methods', 'type'));
    }
}
