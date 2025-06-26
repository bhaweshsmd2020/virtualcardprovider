<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Plan;
use Inertia\Inertia;
use App\Models\Order;
use App\Helpers\PageHeader;
use App\Helpers\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CardHolder;
use App\Models\CardOrder;
use App\Models\CreditCard;
use App\Models\TopUp;
use App\Models\Wallet;
use App\Services\StripeHelper;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\Color\Rgb;
use App\Helpers\CardHelper;
use App\Models\LoginDetail;
use App\Models\Gateway;
use App\Models\CardTopup;
use App\Models\Card;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->will_expire === null && auth()->user()->plan_id != null && session('redirected_plan_payment', false) === false) {
            session()->put('redirected_plan_payment', true);
            Toastr::danger(__('Your subscription is not active or renewed yet. Please make a payment to continue using our services.'));
            return inertia_location(route('user.subscription.payment', auth()->user()->plan_id));
        }

        PageHeader::set()->title(__("Overview"));
        $user = auth()->user()->load('plan');
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalCardOrders = CardOrder::where('user_id', $user->id)->count();
        $totalCreditCards = CreditCard::where('user_id', $user->id)->count();
        $totalWallets = Wallet::where('user_id', $user->id)->count();
        $subscriptions = Order::with('plan')->whereUserId($user->id)->latest()->take(5)->get();
        $recentCardOrders = CardOrder::where('user_id', $user->id)->with('user', 'card')->latest()->take(5)->get();
        $recentTopUps = TopUp::where('user_id', $user->id)->with('user:id,first_name,last_name')->latest()->take(5)->get();

        $filterByCard = request('top_up') ?? 'day';
        $dateFormatCard = match ($filterByCard) {
            'year' => "%Y",
            'month' => "%M %Y",
            'week' => "%a",
            default => "%h",
        };
        $topUpSalesOverview = CardTopup::query()
            ->where('user_id', $user->id)
            ->where('type', 'balance')
            ->selectRaw("DATE_FORMAT(card_topups.created_at,'$dateFormatCard') as date, SUM(card_topups.amount) as sales")
            ->when($filterByCard == 'day', function ($query) {
                $query->whereDate('created_at', today())
                    ->groupByRaw('HOUR(card_topups.created_at)');
            })
            ->when($filterByCard == 'week', function ($query) {
                $start = now()->startOfWeek(Carbon::SATURDAY);
                $end = now()->endOfWeek(Carbon::FRIDAY);
                $query->whereBetween('card_topups.created_at', [$start, $end])
                    ->groupByRaw('DAY(card_topups.created_at)');
            })
            ->when($filterByCard == 'month', function ($query) {
                $query->whereYear('created_at', now()->year)
                    ->groupByRaw('MONTH(card_topups.created_at)');
            })
            ->when($filterByCard == 'year', function ($query) {
                $query->groupByRaw('YEAR(card_topups.created_at)');
            })
            ->orderBy('card_topups.created_at', 'asc')
            ->get();
        $cardholder = CardHolder::query()->where('user_id', auth()->id());
        $creditCards = CreditCard::query()
            ->where('user_id', auth()->id())
            ->with(['user', 'card'])
            ->take(5)->get();
        $card_intro_details = get_option('card_intro_details');
        $stripeCurrency = env('STRIPE_CURRENCY', 'usd');

        $cardHolder = CardHolder::where('user_id', auth()->id())->first();
        if(!empty($cardHolder)){
            $cardHelper = new CardHelper();
            $cardTransactions = $cardHelper->allCardsTransactions($cardHolder->cardholder);
        }else{
            $cardTransactions = null;
        }     
        
        $accountBalance = $user->balance;

        $qrCodeLink = url('/user/top-ups/' . auth()->user()->uuid . '/payment');
        $qrCode = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(
                    new Rgb(255, 255, 255),
                    new Rgb(2, 6, 23)
                )),
                new SvgImageBackEnd
            )
        ))->writeString($qrCodeLink);

        return Inertia::render('User/Dashboard', [
            'accountBalance' => $accountBalance,
            'totalOrders' => $totalOrders,
            'totalCardOrders' => $totalCardOrders,
            'totalCreditCards' => $totalCreditCards,
            'totalWallets' => $totalWallets,
            'subscriptionsOrders' => $subscriptions,
            'recentCardOrders' => $recentCardOrders,
            'recentTopUps' => $recentTopUps,
            'topUpSalesOverview' => $topUpSalesOverview,
            'transactions' => $cardTransactions,
            'authorizations' => $authorizations['authorizations'] ?? [],
            'creditCards' => $creditCards,
            'card_intro_details' => $card_intro_details,
            'stripe_currency' => $stripeCurrency,
            'qrCode' => $qrCode,
            'qrCodeLink' => $qrCodeLink,
            'request' => $request,
            'user' => $user
        ]);
    }

    public function incomePerMonth(Request $request)
    {
        $months = (int) $request->get('months', 6);
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $transactions = CardTopup::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total')
        )
        ->where('user_id', auth()->id())
        ->where('created_at', '>=', $startDate)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthMap = [];
        for ($i = 0; $i < $months; $i++) {
            $monthKey = $startDate->copy()->addMonths($i)->format('Y-m');
            $monthMap[$monthKey] = [
                'month' => $monthKey,
                'income' => 0
            ];
        }

        foreach ($transactions as $raw) {
            if (isset($monthMap[$raw->month])) {
                $monthMap[$raw->month]['income'] = (float) $raw->total;
            }
        }

        return response()->json(array_values($monthMap));
    }

    public function getLastLogin(Request $request)
    {
        $lastLogin = LoginDetail::where('user_id', auth()->id())->where('status', '1')->orderBy('id', 'desc')->first();

        return response()->json([
            'last_login_at' => $lastLogin ? $lastLogin->login_at : null
        ]);
    }
}
