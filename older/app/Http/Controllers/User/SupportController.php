<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Support;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Notification;
use App\Traits\Uploader;

class SupportController extends Controller
{  
    use Uploader;
    public function index(Request $request)
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                'url' => route('user.supports.create'),
            ]
        ];

        $supports = Support::where('user_id', auth()->id())
            ->filterOn(['ticket_no', 'subject', 'status'])
            ->withCount('conversations')
            ->orderBy('created_at', $request->order ?? 'desc')->paginate(20);

        $totalSupports = Support::where('user_id', auth()->id())
            ->count();
        $pendingSupport = Support::where('user_id', auth()->id())
            ->where('status', 2)
            ->count();
        $openSupport = Support::where('user_id', auth()->id())
            ->where('status', 1)
            ->count();
        $closedSupport = Support::where('user_id', auth()->id())
            ->where('status', 0)
            ->count();
        return Inertia::render('User/Support/Index', [
            'supports' => $supports,
            'buttons' => $buttons,
            'totalSupports' => $totalSupports,
            'pendingSupport' => $pendingSupport,
            'openSupport' => $openSupport,
            'closedSupport' => $closedSupport,
        ]);
    }

    public function create()
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                'url' => route('user.supports.create'),
            ]
        ];

        $subjects=[
            "Account related"=>"Account related",
            "Subscription Related"=>"Subscription Related",
            "Transaction Related"=>"Transaction Related",
            "Card Top Attachment field"=>"Card Top Attachment field",
            "Fraud Report"=>"Fraud Report",
            "Others"=>"Others"
        ];

        return Inertia::render('User/Support/Create', [
            'buttons' => $buttons,
            'subjects'=>$subjects,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|max:100|min:10',
            'message' => 'required|max:1000',
        ]);

        $image=null;
        if ($request->hasFile('support_image')) {
            $metaPreview = $this->saveFile($request, 'support_image');
            $image = $metaPreview;
        }

        $support = new Support;
        $support->user_id = auth()->id();
        $support->subject = $request->subject;
        $support->save();

        $support->conversations()->create([
            'comment'  => $request->message,
            'for_admin' => 0,
            'user_id'  => auth()->id(),
            'image'=>$image
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => __('New support ticket created'),
            'comment' => __('A User has created a new support ticket'),
            'url' => url('admin/support/'. $support->id),
        ]);

        return to_route('user.supports.index');
    }

    public function show(string $id)
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-arrow-back"></i>&nbsp' . __('Back'),
                'url' => route('user.supports.index'),
            ]
        ];
        $support = Support::where('user_id', auth()->id())
            ->with(['conversations.user', 'user'])->findOrFail($id);

        return Inertia::render('User/Support/Show', [
            'support' => $support,
            'buttons' => $buttons
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'message' => 'required|max:1000',
        ]);

        $support = Support::where('user_id', auth()->id())->where('id', $id)->first();        

        $support->conversations()->create([
            'comment'  => $request->message,
            'for_admin' => 0,
            'seen' => 0,
            'user_id'  => auth()->id()
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => __('New reply on ticket'),
            'comment' => __('A User has replied on ticket'),
            'url' => url('admin/support/'. $support->id),
        ]);

        return back();
    }
}
