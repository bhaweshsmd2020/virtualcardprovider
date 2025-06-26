<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\KYCRequest;
use App\Helpers\PageHeader;
use App\Helpers\CardHelper;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use App\Mail\KycMail;
use Illuminate\Support\Facades\Mail;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;

class KYCRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:kyc');
    }

    public function index(Request $request)
    {
        PageHeader::set()->title(__('KYC Requests'));
        $all = KYCRequest::count();
        $approved = KYCRequest::where('status', '1')->count();
        $pending = KYCRequest::where('status', '0')->count();
        $rejected = KYCRequest::where('status', '2')->count();
        $reSubmitted = KYCRequest::where('status', '3')->count();

        $requests = KYCRequest::with('user', 'method')
            ->when($request->get('status') !== null, function (Builder $query) use ($request) {
                $type = $request->get('status');
                $query->where('status', '=', $type);
            })
            ->when($request->has('user'), function (Builder $query) use ($request) {
                $query->where('user_id', '=', $request->user);
            })
            ->when($request->get('src') !== null, function (Builder $query) use ($request) {
                $query->whereHas('user', function (Builder $query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->get('src') . '%')
                        ->orWhere('username', 'LIKE', '%' . $request->get('src') . '%')
                        ->orWhere('phone', 'LIKE', '%' . $request->get('src') . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->get('src') . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/KYC/Requests/Index', compact('requests', 'all', 'approved', 'pending', 'rejected', 'reSubmitted'));
    }

    public function store(Request $request)
    {      
        $request->validate([
            'request' => ['required', 'exists:kyc_requests,id'],
            'status' => ['required', Rule::in('approve', 'reject', 'pending')],
        ]);

        $KYCRequest = KYCRequest::findOrFail($request->get('request'));
        $user = $KYCRequest->user; 

        if ($request->get('status') == 'approve') {
            $status = 1;
        } elseif ($request->get('status') == 'reject') {
            $status = 2;

            $KYCRequest->update([
                'status' => $status,
                'rejected_at' => $status == 2 ? today() : null,
            ]);

            $user->kyc_verified_at = null;
            $user->save();

            $template = EmailTemplate::where(['id' => '4', 'type' => 'email'])->select('subject', 'body')->first();

            $template_sub = $template->subject;
            $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
            $template_msg = str_replace('{dashboard}', route('admin.dashboard'), $template_msg); 

            $emailHelper = new EmailHelper();
            $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);

            return back()->with('warning', 'KYC rejected successfully');
        } else {
            $status = 0;
        }        

        DB::transaction(function () use ($request, $status, $KYCRequest) {            

            $cardHelper = new CardHelper();
            $userStatus = $cardHelper->userStatus($KYCRequest->user_id);
            if($userStatus == '2'){
                return back()->with('warning', 'Invite is pending');
            }elseif($userStatus == '3'){
                return back()->with('warning', 'Invite is expired');
            }elseif($userStatus == '1'){
                
                $KYCRequest->update([
                    'status' => $status,
                    'rejected_at' => $status == 2 ? today() : null,
                ]);    
                
                $user = $KYCRequest->user; 

                $identity_request = KYCRequest::where('user_id', $user->id)->where('status', '1')->where('type', 'identity')->first();
                $address_request = KYCRequest::where('user_id', $user->id)->where('status', '1')->where('type', 'address')->first();
                if((!empty($identity_request) && $identity_request->status == '1') && (!empty($address_request) && $address_request->status == '1')){
                    if ($request->get('status') == 'approve' && $user) {
                        $user->kyc_verified_at = now();
                        $user->save();
                    } 

                    $template = EmailTemplate::where(['id' => '3', 'type' => 'email'])->select('subject', 'body')->first();

                    $template_sub = $template->subject;
                    $template_msg = str_replace('{user}', $user->first_name . ' ' . $user->last_name, $template->body);
                    $template_msg = str_replace('{dashboard}', route('admin.dashboard'), $template_msg); 
                    
                    $emailHelper = new EmailHelper();
                    $verify = $emailHelper->sendEmail($user->email, $template_sub, $template_msg);
                }             
                
                if($KYCRequest->type == 'identity'){
                    $message = 'Identity KYC has been verified';
                }else{
                    $message = 'Address KYC has been verified';
                }

                Notification::create([
                    'user_id' => $user->id,
                    'url' => route('user.kyc.show', $KYCRequest),
                    'title' => $message,
                    'comment' => __('Your KYC verification request has been approved successfully'),
                    'for_admin' => 0,
                ]);

                return back()->with('success', 'KYC approved successfully');

            }else{
                return back()->with('warning', $userStatus);
            }            
        });

        return back();
    }

    public function show(KYCRequest $kycRequest)
    {

        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-list"></i>&nbsp&nbsp' . __('Back'),
                'url' => route('admin.kyc-requests.index'),
            ],
        ];
        $kycRequest->load('user', 'method');
        return Inertia::render('Admin/KYC/Requests/Show', compact('kycRequest', 'segments', 'buttons'));
    }

    public function destroy(KYCRequest $KYCRequest)
    {
        $KYCRequest->delete();

        return back();
    }

    public function destroyMass(Request $request)
    {
        foreach ($request->input('ids') as $id) {
            $plan = KYCRequest::findOrFail($id);
            $plan->delete();
        }

        return back();
    }
}
