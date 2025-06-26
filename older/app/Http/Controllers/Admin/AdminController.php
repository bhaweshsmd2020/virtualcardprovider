<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Helpers\Toastr;
use App\Helpers\PageHeader;
use App\Models\Notification;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin-and-roles');
    }

    public function index()
    {
        PageHeader::set()
            ->title(__('Admins'))
            ->buttons([
                [
                    'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                    'url' => route('admin.admin.create')
                ]
            ]);


        $users = User::where('role', 'admin')->with('roles')->where('id', '!=', 1)->latest()->get();

        return Inertia::render('Admin/Users/Admin/Index', compact('users'));
    }

    public function create()
    {

        PageHeader::set()
            ->title(__('Create New Admin'))
            ->buttons([
                [
                    'name' => __('Back'),
                    'url' => route('admin.admin.index')
                ]
            ]);
        $roles = Role::all();
        return Inertia::render('Admin/Users/Admin/Create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:20',
            'last_name' => 'nullable|string|max:20',
            'roles' => 'required',
            'email' => 'required|max:100|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->role = 'admin';
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->roles) {
            $user->assignRole($request->roles);
        }
        Toastr::success('Created Successfully');
        return to_route('admin.admin.index');
    }

    public function edit($id)
    {

        PageHeader::set()
            ->title(__('Edit Admin'))
            ->buttons([
                [
                    'name' => __('Back'),
                    'url' => route('admin.admin.index')
                ]
            ]);

        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return Inertia::render('Admin/Users/Admin/Edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $request->validate([
            'first_name' => 'required|string|max:20',
            'last_name' => 'nullable|string|max:20',
            'email' => 'required|max:100|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);


        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->status = $request->status;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $user->roles()->detach();
        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        Toastr::success('Updated Successfully');

        return to_route('admin.admin.index');
    }

    public function destroy($id)
    {

        User::destroy($id);

        Toastr::danger('Deleted Successfully');

        return back();
    }

    public function adminNotificationsRead(Notification $notification)
    {
        if (!$notification->seen) {
            $notification->seen = 1;
            $notification->save();
        }

        return response()->json([
            'success' => true,
            'redirect_to' => $notification->url ?? false,
        ]);
    }

    public function adminNotificationsClear()
    {
        Notification::query()->where('for_admin', 1)->update(['seen' => 1]);
        Toastr::success('All Notifications Marked As Read');
        return back();
    }
}
