<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\State;
use App\Models\Plan;
use App\Models\User;
use App\Models\Wallet;
use App\Helpers\Toastr;
use App\Models\Country;
use App\Models\Gateway;
use App\Traits\Uploader;
use App\Helpers\PageHeader;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\VirtualCurrency;
use App\Models\CardEmail;
use App\Models\CardLimit;
use App\Models\Card;
use App\Models\LoginDetail;
use App\Models\OtherCity;
use App\Models\OtherState;

class UserController extends Controller
{
    use Uploader;

    public function __construct()
    {
        $this->middleware('permission:users');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::query()->with(['plan', 'cardholder', 'assignedemails'])->where('role', 'user')
            ->when(in_array(request('type'), ['email', 'status']), function ($query) {
                $query->where(request('type'), 'like', '%' . request('search') . '%');
            })
            ->when(request('type') == 'name', function ($query) {
                $query->where('first_name', 'like', '%' . request('search') . '%');
                $query->orWhere('last_name', 'like', '%' . request('search') . '%');
            })
            ->orderBy('id', 'desc')->paginate();
        $totalUsers = User::query()->where('role', 'user')->count();
        $activeUsers = User::query()->where('role', 'user')->where('status', 1)->count();
        $inactiveUsers  =  User::query()->where('role', 'user')->where('status', '!=', 1)->count();

        $plans = Plan::where('status', 1)->get();
        $gateways = Gateway::where('status', 1)->get();
        return inertia('Admin/Users/Index', [
            'users' => $users,
            'plans' => $plans,
            'gateways' => $gateways,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'segments' => request()->segments(),
            'buttons' => [],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::query()->with(['country', 'state', 'city'])
            ->where('role', 'user')->findOrFail($id);

        $totalOrders = $user->orders()->count();
        $totalPendingOrders = $user->orders()->where('status', 2)->count();
        $totalCompleteOrders = $user->orders()->where('status', 1)->count();
        $totalDeclinedOrders = $user->orders()->where('status', 0)->count();

        $orders = $user->orders()->with('user', 'plan', 'gateway')->latest()->paginate(20);
        $card_orders = $user->card_orders()->with('user', 'credit_card:id,card_order_id,card', 'card', 'gateway')->latest()->paginate(20);
        $currencies = VirtualCurrency::query()
            ->with('rates.exchange_currency')
            ->where('status', 'active')
            ->get();
        $wallets = Wallet::query()
            ->where('user_id', $id)
            ->get();

        foreach ($currencies as $currency) {
            $wallet = $wallets->where('virtual_currency_id', $currency->id)->first();
            $has_wallet = $wallet ? true : false;
            $currency_wallets[] = [
                'currency' => $currency->toArray(),
                'wallet' => $has_wallet ? $wallet->toArray() : null,
            ];
        }

        $lastLogin = LoginDetail::where('user_id', $id)->where('status', '1')->orderBy('id', 'desc')->first();
        $othercity = OtherCity::where('user_id', $user->id)->first();
        $otherstate = OtherState::where('user_id', $user->id)->first();

        return inertia('Admin/Users/Show', [
            'user' => $user,
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'totalPendingOrders' => $totalPendingOrders,
            'totalCompleteOrders' => $totalCompleteOrders,
            'totalDeclinedOrders' => $totalDeclinedOrders,
            'card_orders' => $card_orders,
            'accounts' => $currency_wallets ?? [],
            'lastLogin' => $lastLogin,            
            'othercity' => $othercity,
            'otherstate' => $otherstate,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        PageHeader::set(__('Edit User'))->buttons([
            [
                'name' => 'Back',
                'url' => route('admin.users.index'),
                'label' => __('Back'),
            ]
        ]);
        $countries = Country::get(['id', 'name']);
        $cities = City::where('country_id', $user->country_id)->get(['id', 'name']);
        $states = State::where('country_id', $user->country_id)->get(['id', 'name']);
        $cards = Card::where('status', 'active')->get();
        $cardlimits = CardLimit::where('user_id', $user->id)->get();
        $othercity = OtherCity::where('user_id', $user->id)->first();
        $otherstate = OtherState::where('user_id', $user->id)->first();

        $user->load('wallets.currency');

        return inertia('Admin/Users/Edit', [
            'userInfo' => $user,
            'countries' => $countries,
            'cities' => $cities,
            'states' => $states,
            'cards' => $cards,
            'cardlimits' => $cardlimits,
            'othercity' => $othercity,
            'otherstate' => $otherstate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $updateData = $request->merge([
            'status' => $request->get('status') ? 1 : 0,
            'email_verified_at' => $request->get('email_verified_at') ? now() : null,
            'phone_verified_at' => $request->get('phone_verified_at') ? now() : null,
            'kyc_verified_at' => $request->get('kyc_verified_at') ? now() : null,
        ])->toArray();

        
        if ($request->has('card_limits')) {
            foreach ($request->card_limits as $card_id => $limit) {
                CardLimit::updateOrCreate(
                    [
                        'user_id' => $user->id, 
                        'card_type' => $card_id
                    ],
                    [
                        'limit' => $limit
                    ]
                );
            }
        }
        
        if ($request->has('topup_limit')) {
            foreach ($request->topup_limit as $card_id => $limit) {
                CardLimit::updateOrCreate(
                    [
                        'user_id' => $user->id, 
                        'card_type' => $card_id
                    ],
                    [
                        'topup_limit' => $limit
                    ]
                );
            }
        }

        if ($request->has('spending_limit')) {
            foreach ($request->spending_limit as $card_id => $limit) {
                CardLimit::updateOrCreate(
                    [
                        'user_id' => $user->id, 
                        'card_type' => $card_id
                    ],
                    [
                        'spending_limit' => $limit
                    ]
                );
            }
        }

        if ($password = $request->get('password')) {
            $updateData['password'] = $password;
        }

        // upload avatar
        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $this->uploadFile('avatar', $user->avatar);
        }

        $wallets = $request->get('wallets', []);
        DB::transaction(function () use ($user, $updateData, $wallets) {
            foreach ($wallets as $wallet) {
                $user
                    ->wallets()
                    ->where('id', $wallet['id'])
                    ->update([
                        'balance' => $wallet['balance'],
                    ]);
            }
            $user->update($updateData);
        });


        Toastr::success(__('User information has been successfully updated'));

        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function assignPlan(Request $request, User $user)
    {
        $request->validate([
            'user_id' => ['required'],
            'plan_id' => ['required'],
            'will_expire' => ['required'],
        ]);

        $plan = Plan::where('status', 1)->where('price', '>', 0)->findOrFail($request->plan_id);

        $user->plan_id = $plan->id;
        $user->plan_data = $plan->data;
        $user->will_expire = $request->get('will_expire', now()->addDays($plan->days));
        $user->save();

        Notification::sendFromAdmin(
            $user->id,
            title: __('Plan Updated'),
            message: __('new plan assigned by assigned'),
        );


        Session::flash('success', __('Plan has been assigned successfully'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        CardEmail::where('user_id', $user->id)->update([
            'user_id' => null,
            'card_user_id' => null,
            'invite_id' => null,
            'status' => 0,
        ]);

        $this->removeFile($user->avatar);
        $user->delete();
        Toastr::success('User has been deleted successfully');
        return back();
    }
}
