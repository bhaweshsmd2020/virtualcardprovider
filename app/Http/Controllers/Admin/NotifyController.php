<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Traits\Notifications;
use App\Models\User;
use Auth;
use Inertia\Inertia;

class NotifyController extends Controller
{

    use Notifications;

    public function __construct()
    {
        $this->middleware('permission:notification');
    }
    public function index(Request $request)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Create Notification'),
                'url' => '#',
                'target' => '#addNewNotificationDrawer'
            ]
        ];

        $notifications = Notification::query();

        if ($request->filled('search')) {
            if ($request->type == 'user_email') {
                $notifications = $notifications->whereHas('user', function ($q) use ($request) {
                    return $q->where('email', $request->search);
                });
            } else {
                $notifications = $notifications->where($request->type, 'LIKE', '%' . $request->search . '%');
            }
        }

        $notifications = $notifications->with('user')->where('for_admin', 1)->latest()->paginate(20);
        $type = $request->type ?? 'email';

        $totalNotifications = Notification::count();
        $readNotifications  = Notification::where('seen', 1)->count();
        $unreadNotifications = Notification::where('seen', 0)->count();

        return Inertia::render('Admin/Notification/Index', [
            'notifications' => $notifications,
            'request' => $request,
            'type' => $type,
            'totalNotifications' => $totalNotifications,
            'readNotifications' => $readNotifications,
            'unreadNotifications' => $unreadNotifications,
            'buttons' => $buttons,
            'segments' => $segments,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100',
            'email' => 'required|email|exists:users,email',
            'description'  => 'required',
            'url'  => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        $title = $request->title;
        $notification['user_id']   = $user->id;
        $notification['title']     = $title;
        $notification['comment']   = $request->description;
        $notification['url']       = $request->url;

        $this->createNotification($notification);

        return back();
    }

    public function destroy($id)
    {
        $row = Notification::findOrFail($id);
        $row->delete();

        return redirect()->back();
    }
}
