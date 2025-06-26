<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:withdraw');
    }

    public function index(Request $request)
    {
        $withdraws = Withdraw::query()
            ->filterOn(['id', 'amount'])
            ->filterOnRelation(['user_email', 'receiver_email'])
            ->with('receiver:id,email', 'user')
            ->paginate();

        $withdrawAmounts = Withdraw::select('virtual_currency_id', DB::raw('SUM(amount) as total_amount'))
            ->with('currency:id,currency,preview')
            ->groupBy('virtual_currency_id')
            ->get();

        return Inertia::render('Admin/Withdraw/Index', [
            'withdraws' => $withdraws,
            'request' => $request,
            'withdrawAmounts' => $withdrawAmounts
        ]);
    }
}
