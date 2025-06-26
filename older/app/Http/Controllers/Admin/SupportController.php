<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Support;
use App\Models\Supportlog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Traits\Uploader;

class SupportController extends Controller
{  
    use Uploader;
    public function __construct()
    {
        $this->middleware('permission:support');
    }

    public function index(Request $request)
    {
        $segments = request()->segments();
        $buttons = [];
        $supports = Support::query();
        if (!empty($request->search)) {
            if ($request->type == 'user_email') {
                $supports = $supports->whereHas('user', function ($q) use ($request) {
                    return $q->where('email', $request->search);
                });
            } else {
                $supports = $supports->where($request->type, 'LIKE', '%' . $request->search . '%');
            }
        }
        $supports = $supports->with('user')->withCount('conversations')->latest()->paginate(20);

        $pendingSupport = Support::where('status', 2)->count();
        $openSupport = Support::where('status', 1)->count();
        $closedSupport = Support::where('status', 0)->count();
        $totalSupports = $pendingSupport + $openSupport + $closedSupport;

        $type = $request->type ?? 'email';
        $invoice = get_option('invoice_data');
        $currency = get_option('base_currency');
        $tax = get_option('tax');
        return Inertia::render('Admin/Support/Index', [
            'request' => $request,
            'supports' => $supports,
            'pendingSupport' => $pendingSupport,
            'openSupport' => $openSupport,
            'closedSupport' => $closedSupport,
            'totalSupports' => $totalSupports,
            'type' => $type,
            'segments' => $segments,
            'buttons' => $buttons,
            'invoice' => $invoice,
            'currency' => $currency,
            'tax' => $tax
        ]);
    }

    public function show($id)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => __('Back'),
                'url' => route('admin.support.index'),
            ]
        ];
        $support = Support::with('conversations.user', 'user')->findOrFail($id);
        Supportlog::where('is_admin', 0)
            ->where('support_id', $id)
            ->update([
                'seen' => 1
            ]);

        return Inertia::render('Admin/Support/Show', [
            'support' => $support,
            'segments' => $segments,
            'buttons' => $buttons,
        ]);
    }


    public function update(Request $request, $id)
    {
        $support = Support::findOrFail($id);
        $support->status = $request->status;
        $support->save();
        if($request->filled('message')) {
            $request->validate([
                'message' => 'required|max:1000',
            ]);
            $image=null;
            if ($request->hasFile('support_image')) {
                $metaPreview = $this->saveFile($request, 'support_image');
                $image = $metaPreview;
            }        

            $support->conversations()->create([
                'comment' => $request->message,
                'is_admin' => 1,
                'seen' => 0,
                'user_id' => Auth::id(),
                'image'=>$image
            ]);

            Notification::create([
                'user_id' => $support->user_id,
                'url' => url('user/supports/'. $support->id),
                'title' => __('New reply on ticket'),
                'comment' => __('Admin has replied on your ticket'),
                'for_admin' => 0,
            ]);
        }

        return back();
    }
}
