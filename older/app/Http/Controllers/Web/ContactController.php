<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Helpers\SeoMeta;
use App\Helpers\Toastr;
use App\Traits\Seo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Helpers\EmailHelper;
use App\Models\EmailTemplate;
use App\Models\EmailConfig;
use App\Models\Country;

class ContactController extends Controller
{
    use Seo;

    // public function index()
    // {
    //     $contact = get_option('contact_page', true);

    //     SeoMeta::init('seo_contact');
    //     return Inertia::render('Web/Contact', [
    //         'contact' => $contact,
    //     ]);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:40'],
    //         'email' => ['required', 'email', 'max:40'],
    //         'phone' => 'required|max:100',
    //         'message' => 'required|max:500',
    //     ]);

    //     try {
    //         $data['name'] = $request->name;
    //         $data['email'] = $request->email;
    //         $data['phone'] = $request->phone;
    //         $data['message'] = $request->message;

    //         $emailConfig = EmailConfig::where(['email_protocol' => 'smtp', 'status' => '1'])->first();
    //         $template = EmailTemplate::where(['id' => '12', 'type' => 'email'])->select('subject', 'body')->first();

    //         $template_sub = $template->subject;
    //         $template_msg = str_replace('{today}', now(), $template->body); 
    //         $template_msg = str_replace('{name}', $request->name, $template_msg);
    //         $template_msg = str_replace('{email}', $request->email, $template_msg); 
    //         $template_msg = str_replace('{phone}', $request->phone, $template_msg); 
    //         $template_msg = str_replace('{message}', $request->message, $template_msg); 

    //         $emailHelper = new EmailHelper();
    //         $verify = $emailHelper->sendEmail($emailConfig->notification_email, $template_sub, $template_msg);

    //         Toastr::success(__('Message sent successfully'));
    //     } catch (Exception $e) {
    //         Toastr::danger($e->getMessage());
    //     }

    //     return back();
    // }
    public function index()
    {
        $contact = get_option('contact_page', true);
        $countries=Country::where('status','active')->get();

        $cards=["0 — 1000","1000 — 5000","5000 — 10000","10000 — 100000","100K+"];
        $turnover=["10-5000 USD","5000-10000 USD","10000-20000 USD","20000-50000 USD","50000-100000 USD",">100K USD"];
        $bussinesses=[
           "Activities of Extraterritorial Organizations and Bodies",
           "Administrative Activity",
           "Agriculture, Forestry and Fishing",
           "Art, Entertainment and Recreation",
           "Building",
           "Education",
           "Energy",
           "Financial and Insurance Services",
           "Health Care and Social Services",
           "Information and Communication",
           "Lodging and Catering Services",
           "Manufacturing",
           "Mining Industry",
           "Other Services Provision",
           "Professional, Scientific and Technical Activities",
           "Public Administration and Defense",
           "Real Estate Transactions",
           "Renovation",
           "Transportation and Storage",
           "Water Supply, Sewerage, Waste Collection and Distribution",
           "Wholesale and Retail Trade",
           "Other"

        ]; 
        SeoMeta::init('seo_contact');
        return Inertia::render('Web/Contact', [
            'contact' => $contact,
            'countries'=>$countries,
            'turnover'=>$turnover,
            'cards'=>$cards,
            'bussinesses'=>$bussinesses
        ]);
    }

    public function store(Request $request)
    { 
        $request->validate([
            'name' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:40'],
            'phonecode'=>'required',
            'phone' => 'required|max:100',
            'company_name'=>'required',
            'company_website'=>'required',
            'country'=>'required',
            'turnover'=>'required',
            'card'=>'required',
            'bussiness'=>'required',
            'message' => 'required|max:500',
        ]);

        try {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['phone'] = $request->phonecode.$request->phone;
            $data['company_name'] = $request->company_name;
            $data['company_website'] = $request->company_website;
            $data['country'] = $request->country;
            $data['turnover'] = $request->turnover;
            $data['card'] = $request->card;
            $data['bussiness'] = $request->bussiness;
            $data['message'] = $request->message;

            $emailConfig = EmailConfig::where(['email_protocol' => 'smtp', 'status' => '1'])->first();
            $template = EmailTemplate::where(['id' => '12', 'type' => 'email'])->select('subject', 'body')->first();
        
            $template_sub = $template->subject;
            $template_msg = str_replace('{today}', now(), $template->body); 
            $template_msg = str_replace('{name}', $request->name, $template_msg);
            $template_msg = str_replace('{email}', $request->email, $template_msg); 
            $template_msg = str_replace('{phone}', $request->phonecode.$request->phone, $template_msg); 
            $template_msg = str_replace('{country}', $request->country, $template_msg); 
            $template_msg = str_replace('{company}', $request->company_name, $template_msg); 
            $template_msg = str_replace('{website}', $request->company_website, $template_msg); 
            $template_msg = str_replace('{turnover}', $request->turnover, $template_msg); 
            $template_msg = str_replace('{card}', $request->card, $template_msg); 
            $template_msg = str_replace('{business}', $request->bussiness, $template_msg); 
            $template_msg = str_replace('{message}', $request->message, $template_msg); 

            $emailHelper = new EmailHelper();
            $verify = $emailHelper->sendEmail($emailConfig->notification_email, $template_sub, $template_msg);

            Toastr::success(__('Message sent successfully'));
        } catch (Exception $e) {
            Toastr::danger($e->getMessage());
        }

        return back();
    }
}
