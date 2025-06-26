<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Helpers\Toastr;
use App\Helpers\PageHeader;
use App\Models\Notification;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin-and-roles');
    }

    public function index()
    {
        PageHeader::set()
            ->title(__('Email Templates'))
            ->buttons([
                [
                    'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                    'url' => route('admin.email-templates.create')
                ]
            ]);


        $emailtemplates = EmailTemplate::get();

        return Inertia::render('Admin/EmailTemplate/Index', compact('emailtemplates'));
    }

    public function create()
    {
        PageHeader::set()
            ->title(__('Create New Email Template'))
            ->buttons([
                [
                    'name' => __('Back'),
                    'url' => route('admin.email-templates.index')
                ]
            ]);
        return Inertia::render('Admin/EmailTemplate/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'body' => 'required',
            'status' => 'required'
        ]);

        $emailtemplate = new EmailTemplate();
        $emailtemplate->subject = $request->subject;
        $emailtemplate->body = $request->body;
        $emailtemplate->status = $request->status;
        $emailtemplate->type = 'email';
        $emailtemplate->save();

        Toastr::success('Email Template Created Successfully');
        return to_route('admin.email-templates.index');
    }

    public function edit($id)
    {
        PageHeader::set()
            ->title(__('Edit Email Template'))
            ->buttons([
                [
                    'name' => __('Back'),
                    'url' => route('admin.email-templates.index')
                ]
            ]);

        $emailtemplate = EmailTemplate::findOrFail($id);
        return Inertia::render('Admin/EmailTemplate/Edit', compact('emailtemplate'));
    }

    public function update(Request $request, $id)
    {        
        $request->validate([
            'subject' => 'required',
            'body' => 'required',
            'status' => 'required'
        ]);

        $emailtemplate = EmailTemplate::find($id);

        $emailtemplate->subject = $request->subject;
        $emailtemplate->body = $request->body;
        $emailtemplate->status = $request->status;
        $emailtemplate->save();

        Toastr::success('Email Template Updated Successfully');
        return to_route('admin.email-templates.index');
    }

    public function destroy($id)
    {
        EmailTemplate::destroy($id);

        Toastr::danger('Email Template Deleted Successfully');
        return back();
    }
}
