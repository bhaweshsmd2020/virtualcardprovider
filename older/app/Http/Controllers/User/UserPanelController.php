<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Helpers\Toastr;
use App\Traits\Uploader;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Google2FA;
use App\Models\CardEmail;

class UserPanelController extends Controller
{
    use Uploader;

    public function accountSetting()
    {
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        $google2fa = [
            'enabled' => $user->hasEnabledTwoFactorAuthentication(),
            'secret' => $user->google2fa_secret,
            'qrCodeUrl' => '',
        ];

        if (!$google2fa['enabled']) {
            $google2faClient = new Google2FA(request());
            $google2fa['secret'] = session('google2fa_secret', $google2faClient->generateSecretKey());
            $google2fa['qrCodeUrl'] = $google2faClient->getQRCodeInline(
                config('app.name'),
                '',
                $google2fa['secret']
            );

            session()->put('google2fa_secret', $google2fa['secret']);
        }


        return Inertia::render('User/AccountSettings', [
            'user' => $user,
            'google2fa' => $google2fa,
        ]);
    }

    public function accountSettingUpdate(Request $request)
    {
        $request->merge([
            'full_name' => $request->first_name . ' ' . $request->last_name,
        ]);
        $request->validate([
            'full_name' => ['required', 'string', 'max:20'],
            'first_name' => ['required', 'string'],
            'last_name' => ['nullable', 'string'],
            'email' => [request()->user()->provider_id ? 'nullable' : 'required', 'email', 'max:255', 'unique:users,email,' . request()->user()->id],
            'current_password' => [request()->user()->provider_id ? 'nullable' : 'required', 'current_password'],
            'phone' => ['required', 'string', 'unique:users,phone,' . request()->user()->id],
        ]);
        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        $user->update([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
        ]);

        return back();
    }

    public function changePassword()
    {
        if (request()->user()->provider_id) {
            return back();
        }
        $segments = request()->segments();
        return Inertia::render('User/PasswordChange', [
            'segments' => $segments,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed'],
        ]);

        /**
         * @var \App\Models\User
         */
        $user = auth()->user();
        $user->password = $request->get('new_password');
        $user->save();

        Toastr::success(__('Password Changed Successfully'));

        return back();
    }


    // destroy the user | soft deleted
    public function destroy()
    {
        /**
         * @var \App\Models\User
         */        
        
        $user = auth()->user();

        CardEmail::where('user_id', $user->id)->update([
            'user_id' => null,
            'card_user_id' => null,
            'invite_id' => null,
            'status' => 0,
        ]);

        $user->deleted_at = now();
        $user->save();

        Auth::logout();

        return Inertia::location('/');
    }

    public function userNotifications()
    {
        return
            request()
            ->user()
            ->hasMany(Notification::class)
            ->where('for_admin', 1)
            ->limit(5)
            ->get()->map(function ($item) {
                $item->title_short = Str::limit($item->title, 30, '...');
                $item->comment_short = Str::limit($item->comment, 35, '...');
                return $item;
            });
    }

    public function userNotificationsRead(Notification $notification)
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

    public function userNotificationsClear()
    {

        /**
         * @var \App\Models\User
         */
        $user = auth()->user();

        Notification::query()
            ->where('user_id', $user->id)
            ->where('for_admin', 0)
            ->update(['seen' => 1]);

        Toastr::success('All Notifications Marked As Read');
        return back();
    }

    public function ExchangeTransaction(Notification $notification)
    {
        $notification->seen = 1;
        $notification->save();
        return response()->json([
            'success' => true,
        ]);
    }
}
