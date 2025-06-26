<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PageHeader;
use App\Helpers\Toastr;
use App\Http\Controllers\Controller;
use App\Models\KycMethod;
use App\Traits\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class KycMethodController extends Controller
{
    use Uploader;

    public function __construct()
    {
        $this->middleware('permission:kyc');
    }


    public $types = ['text', 'number', 'email', 'tel', 'textarea', 'file'];

    public function index()
    {
        PageHeader::set()->title(__('KYC Methods'))->buttons([
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp&nbsp' . __('Create New Method'),
                'url' => route('admin.kyc-methods.create'),
            ],
        ]);

        $all = KycMethod::query()->count();
        $active = KycMethod::query()->where('status', 1)->count();
        $inactive = KycMethod::query()->where('status', 0)->count();

        $kycMethods = KycMethod::latest()->paginate(20);
        $KYC_VERIFICATION = env('KYC_VERIFICATION', true);
        return Inertia::render('Admin/KYC/Methods/Index', compact('kycMethods', 'all', 'active', 'inactive', 'KYC_VERIFICATION'));
    }

    public function create()
    {

        PageHeader::set()->title(__('Create New Method'))->buttons(
            [
                [
                    'name' => '<i class="bx bx-list-ol"></i>&nbsp&nbsp' . __('Back to list'),
                    'url' => route('admin.kyc-methods.index'),
                ],
            ]
        );

        $types = $this->types;
        return Inertia::render('Admin/KYC/Methods/Create', compact('types'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'image' => ['required', 'image'],
            'title' => ['required', 'string'],
            'image_accept' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
            'type' => ['required', 'boolean'],
            'fields' => ['required', 'array'],
            'fields.*.label' => ['required', 'string'],
            'fields.*.type' => ['required', 'string', Rule::in($this->types)],
        ]);

        KycMethod::create([
            'title' => $request->input('title'),
            'image' => $this->saveFile($request, 'image'),
            'image_accept' => $request->input('image_accept'),
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'fields' => $request->input('fields'),
        ]);

        Toastr::success(__('Kyc Method Created Successfully'));

        return to_route('admin.kyc-methods.index');
    }

    public function edit(KycMethod $kycMethod)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-list-ol"></i>&nbsp&nbsp' . __('Back to list'),
                'url' => route('admin.kyc-methods.index'),
            ],
        ];

        $types = $this->types;
        return Inertia::render('Admin/KYC/Methods/Edit', compact('kycMethod', 'types', 'segments', 'buttons'));
    }

    public function update(Request $request, KycMethod $kycMethod)
    {
        $request->validate([
            'image' => ['nullable', 'image'],
            'title' => ['required', 'string'],
            'image_accept' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
            'type' => ['required', 'boolean'],
            'fields' => ['required', 'array'],
            'fields.*.label' => ['required', 'string'],
            'fields.*.type' => ['required', 'string', Rule::in($this->types)],
        ]);

        if ($request->hasFile('image')) {
            $this->removeFile($kycMethod->image);
        }

        $kycMethod->update([
            'title' => $request->input('title'),
            'image' => $request->hasFile('image') ? $this->saveFile($request, 'image') : $kycMethod->image,
            'image_accept' => $request->input('image_accept'),
            'status' => $request->input('status'),
            'type' => $request->input('type'),
            'fields' => $request->input('fields'),
        ]);

        return to_route('admin.kyc-methods.index');
    }

    public function massDestroy(Request $request)
    {
        foreach ($request->ids as $id) {
            if ($kycMethod = KycMethod::find($id)) {
                if (file_exists($kycMethod->image)) {
                    Storage::delete($kycMethod->image);
                }
                $kycMethod->delete();
            }
        }
        Toastr::success(__('Kyc Methods Deleted Successfully'));
        return to_route('admin.kyc-methods.index');
    }
}
