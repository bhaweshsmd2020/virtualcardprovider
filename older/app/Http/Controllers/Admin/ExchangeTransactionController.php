<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExchangeTransactionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:exchanges');
    }

    public function index(Request $request)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-file"></i>&nbsp&nbsp' . __('Conversion Details'),
                'url' => '#',
                'target' => '#infoDrawer',
            ],
        ];
        $exchangeTransactions = ExchangeTransaction::query()
            ->filterOn(['id', 'from_amount'])
            ->filterOnRelation(['user_email'])

            ->with('user')
            ->latest()->paginate(20);

        $type = $request->type ?? 'email';
        $exchange_details = get_option('exchange_details');
        $totalServiceFee = ExchangeTransaction::sum('service_fee');
        $total = ExchangeTransaction::count();
        $todayExchanges = ExchangeTransaction::whereDate('created_at', date('Y-m-d'))->count();
        return Inertia::render('Admin/ExchangeTransaction/Index', [
            'segments' => $segments,
            'exchangeTransactions' => $exchangeTransactions,
            'request' => $request,
            'type' => $type,
            'buttons' => $buttons,
            'exchange_details' => $exchange_details,
            'totalServiceFee' => $totalServiceFee,
            'total' => $total,
            'todayExchanges' => $todayExchanges,
        ]);
    }
}
