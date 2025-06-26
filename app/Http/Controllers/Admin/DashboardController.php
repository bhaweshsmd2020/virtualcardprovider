<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Card;
use App\Models\Plan;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\TopUp;
use App\Models\Wallet;
use App\Helpers\Toastr;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Models\CardOrder;
use App\Models\CreditCard;
use App\Helpers\PageHeader;
use Illuminate\Http\Request;
use App\Services\StripeHelper;
use Illuminate\Support\Facades\DB;
use App\Models\ExchangeTransaction;
use App\Models\TransferTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    private function getChartOverview($modelClass, $filterBy, $dateColumn, $serviceColumn)
    {
        $dateFormatMap = [
            'year' => "%Y",
            'month' => "%M %Y",
            'week' => "%a",
            'day' => "%h",
        ];

        $dateFormat = $dateFormatMap[$filterBy] ?? "%h";

        $query = $modelClass::query()
            ->selectRaw("DATE_FORMAT($dateColumn, '$dateFormat') as date, SUM($serviceColumn) as sales");

        if ($filterBy === 'day') {
            $query->whereDate($dateColumn, today())
                ->groupByRaw("HOUR($dateColumn)");
        } elseif ($filterBy === 'week') {
            $start = now()->startOfWeek(Carbon::SATURDAY);
            $end = now()->endOfWeek(Carbon::FRIDAY);
            $query->whereBetween($dateColumn, [$start, $end])
                ->groupByRaw("DAY($dateColumn)");
        } elseif ($filterBy === 'month') {
            $query->whereYear($dateColumn, now()->year)
                ->groupByRaw("MONTH($dateColumn)");
        } elseif ($filterBy === 'year') {
            $query->groupByRaw("YEAR($dateColumn)");
        }

        return $query->orderBy($dateColumn, 'asc')->get();
    }
    public function __invoke(Request $request, StripeHelper $stripeHelper)
    {
        PageHeader::set()
            ->title(__("Dashboard"));

        $totalOrders = Order::count();
        $totalCardOrders = CardOrder::count();
        $totalCreditCards = CreditCard::count();
        $totalWallets = Wallet::count();
        $totalSales = Order::sum('amount');
        $popularPlans = Plan::whereHas('orders')
            ->withCount('activeuser')
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->withSum('orders', 'amount')
            ->get()
            ->map(function ($query) {
                return [
                    'name' => $query->title,
                    'activeuser' => number_format($query->activeuser_count),
                    'orders_count' => number_format($query->orders_count),
                    'total_amount' => amount_format($query->orders_sum_amount, 'icon'),
                ];
            });

        $mostOrderedPlan = Plan::query()
            ->select('id', 'title', 'price', 'days')
            ->where('status', 1)
            ->when($request->plan === 'today', function ($query) {
                $query->whereHas('orders', function ($q) {
                    $q->whereDate('created_at', Carbon::today());
                });
            })
            ->when($request->plan === 'month', function ($query) {
                $query->whereHas('orders', function ($q) {
                    $q->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', Carbon::now()->month);
                });
            })
            ->wherehas('orders')
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->first();
        $mostOrderedCard = Card::query()
            ->select('id', 'title', 'card_variant')
            ->where('status', 1)
            ->whereHas('card_orders')
            ->when($request->card === 'today', function ($query) {
                $query->whereHas('card_orders', function ($q) {
                    $q->whereDate('created_at', Carbon::today());
                });
            })
            ->when($request->card === 'month', function ($query) {
                $query->whereHas('card_orders', function ($q) {
                    $q->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', Carbon::now()->month);
                });
            })
            ->withCount('card_orders')
            ->orderByDesc('card_orders_count')
            ->take(2)
            ->get();
        $recentOrders = Order::whereHas('user')
            ->whereHas('plan')
            ->with('user:id,avatar,first_name,last_name,created_at', 'plan:id,title')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($query) {
                return [
                    'avatar' => $query->user->avatar ? asset($query->user->avatar) : 'https://ui-avatars.com/api/?name=' . $query->user->name,
                    'name' => $query->user->name,
                    'plan' => $query->plan->title,
                    'invoice' => $query->invoice_no,
                    'amount' => amount_format($query->amount, 'icon'),
                    'created_at' => $query->created_at->diffForHumans(),
                    'link' => url('admin/order/' . $query->id),
                ];
            });
        $recentCardOrders = CardOrder::whereHas('user')
            ->with(['user:id,avatar,first_name,last_name', 'card'])
            ->latest()->take(8)->get();
        $recentDeposits = Deposit::with('user:id,first_name,last_name')->latest()->take(5)->get();
        $recentTopUps = TopUp::with('user:id,first_name,last_name')
            ->latest()->take(5)->get();
        // chart
        $filterBy = request('sales') ?? 'day';
        $salesOverview = $this->getChartOverview(
            Order::class,
            $filterBy,
            'orders.created_at',
            'orders.amount'
        );
        $filterByCard = request('card_sales') ?? 'day';
        $cardSalesOverview = $this->getChartOverview(
            CardOrder::class,
            $filterByCard,
            'card_orders.created_at',
            'card_orders.amount'
        );

        $authorizations = $stripeHelper->authorizations('user.credit-card.authorizations');
        $transactions = $stripeHelper->transactions('user.credit-card.transactions');

        $walletBalances = Wallet::select('virtual_currency_id', DB::raw('SUM(balance) as total_balance'))
            ->with('currency:id,currency,preview,precision')
            ->groupBy('virtual_currency_id')
            ->get();
        // service fees
        $filterByWithdraw = request('withdraw') ?? 'day';
        $withdrawServiceFeeOverview = $this->getChartOverview(
            Withdraw::class,
            $filterByWithdraw,
            'withdraws.created_at',
            'withdraws.service_fee'
        );
        $filterByTransfer = request('transfer') ?? 'day';
        $transferServiceFeeOverview = $this->getChartOverview(
            TransferTransaction::class,
            $filterByTransfer,
            'transfer_transactions.created_at',
            'transfer_transactions.service_fee'
        );
        $filterByExchange = request('exchange') ?? 'day';
        $exchangeServiceFeeOverview = $this->getChartOverview(
            ExchangeTransaction::class,
            $filterByExchange,
            'exchange_transactions.created_at',
            'exchange_transactions.service_fee'
        );
        $filterByTopUp = request('top_up') ?? 'day';
        $topUpTransactionFeeOverview = $this->getChartOverview(
            TopUp::class,
            $filterByTopUp,
            'top_ups.created_at',
            'top_ups.transaction_fee'
        );
        $filterByDeposit = request('deposit') ?? 'day';
        $depositFeeOverview = $this->getChartOverview(
            Deposit::class,
            $filterByDeposit,
            'deposits.created_at',
            'deposits.deposit_fee'
        );
        $stripeCurrency = env('STRIPE_CURRENCY', 'usd');
        return Inertia::render('Admin/Dashboard', [
            'totalOrders' => $totalOrders,
            'totalCardOrders' => $totalCardOrders,
            'totalCreditCards' => $totalCreditCards,
            'totalWallets' => $totalWallets,
            'totalSales' => amount_format($totalSales, ''),
            'mostOrderedPlan' => $mostOrderedPlan,
            'mostOrderedCard' => $mostOrderedCard,
            'popularPlans' => $popularPlans,
            'recentOrders' => $recentOrders,
            'recentCardOrders' => $recentCardOrders,
            'salesOverview' => $salesOverview,
            'cardSalesOverview' => $cardSalesOverview,
            'recentDeposits' => $recentDeposits,
            'recentTopUps' => $recentTopUps,
            'transactions' => $transactions['transactions'] ?? [],
            'authorizations' => $authorizations['authorizations'] ?? [],
            'request' => $request,
            'walletBalances' => $walletBalances,
            'withdrawServiceFeeOverview' => $withdrawServiceFeeOverview,
            'transferServiceFeeOverview' => $transferServiceFeeOverview,
            'exchangeServiceFeeOverview' => $exchangeServiceFeeOverview,
            'topUpTransactionFeeOverview' => $topUpTransactionFeeOverview,
            'depositFeeOverview' => $depositFeeOverview,
            'stripe_currency' => $stripeCurrency
        ]);
    }
}
