<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransferController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:transfers');
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
        $transferTransactions = TransferTransaction::query()
            ->filterOn(['id', 'from_amount'])
            ->filterOnRelation(['user_email'])
            ->with('user')
            ->latest()->paginate(20);

        $totalTransferTransaction = TransferTransaction::count();
        $totalServiceFee = TransferTransaction::sum('service_fee');
        $todayTransfers = TransferTransaction::whereDate('created_at', date('Y-m-d'))->count();
        $type = $request->type ?? 'email';
        $transfer_details = get_option('transfer_details');
        return Inertia::render('Admin/Transfer/Index', [
            'segments' => $segments,
            'transferTransactions' => $transferTransactions,
            'request' => $request,
            'totalTransferTransaction' => $totalTransferTransaction,
            'totalServiceFee' => $totalServiceFee,
            'todayTransfers' => $todayTransfers,
            'type' => $type,
            'buttons' => $buttons,
            'transfer_details' => $transfer_details
        ]);
    }
}
