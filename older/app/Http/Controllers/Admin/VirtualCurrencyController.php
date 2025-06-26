<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PageHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\VirtualCurrency\VirtualCurrencyStoreRequest;
use App\Http\Requests\VirtualCurrency\VirtualCurrencyUpdateRequest;
use App\Models\ExchangeRate;
use App\Models\VirtualCurrency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VirtualCurrencyController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:virtual-currency');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        PageHeader::set()
            ->title('Virtual Currencies')
            ->buttons([
                [
                    'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                    'url' => route('admin.virtual-currency.create'),
                ]
            ]);
        $virtualCurrencies = VirtualCurrency::where('is_default', 0)
            ->paginate();

        $total = VirtualCurrency::count();
        $totalActive = VirtualCurrency::where('status', 'active')->count();
        $totalInactive = VirtualCurrency::where('status', 'inactive')->count();
        return Inertia::render('Admin/VirtualCurrency/Index', [
            'virtualCurrencies' => $virtualCurrencies,
            'total' => $total,
            'totalActive' => $totalActive,
            'totalInactive' => $totalInactive,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currencies = VirtualCurrency::all()->map(function ($currency) {
            return [
                'id' => $currency->id,
                'currency' => $currency->currency,
            ];
        });
        return Inertia::render('Admin/VirtualCurrency/Create', [
            'currencies' => $currencies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VirtualCurrencyStoreRequest $request)
    {
        if (request('is_default') == 1 || request('is_default') == true) {
            VirtualCurrency::query()->update(['is_default' => 0]);
        }
        $currency = VirtualCurrency::create(array_merge($request->validated(), [
            'currency' => str()->lower($request->currency),
        ]));
        if (request()->filled('rates')) {
            foreach (request()->rates as $rate) {
                $request->validate(['rates' => 'array']);
                $currency->rates()->create([
                    'rate' => $rate['rate'],
                    'virtual_currency_exchange_id' => $rate['id']
                ]);
            }
        }
        return redirect()->route('admin.virtual-currency.index')
            ->with('success', 'Currency created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $virtualCurrency = VirtualCurrency::findOrFail($id);
        $rates = ExchangeRate::query()
            ->where('virtual_currency_id', $virtualCurrency->id)
            ->with('exchange_currency:id,currency')
            ->get();
        $currencies = VirtualCurrency::where('id', '!=', $virtualCurrency->id)
            ->get()
            ->map(function ($currency) {
                return [
                    'id' => $currency->id,
                    'currency' => $currency->currency,
                ];
            });
        return Inertia::render('Admin/VirtualCurrency/Edit', [
            'virtualCurrency' => $virtualCurrency,
            'rates' => $rates,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VirtualCurrencyUpdateRequest $request, VirtualCurrency $virtualCurrency)
    {
        $virtualCurrency->update(array_merge($request->validated(), [
            'currency' => str()->lower($request->currency),
            'is_default' => 0
        ]));

        if (request()->filled('rates')) {
            $request->validate(['rates' => 'array']);
            foreach (request()->rates as $rate) {
                $virtualCurrency->rates()->updateOrCreate([
                    'virtual_currency_exchange_id' => $rate['id'],
                ], ['rate' => $rate['rate']]);
            }
        }
        return redirect()->route('admin.virtual-currency.index')
            ->with('success', __('Currency updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VirtualCurrency $virtualCurrency)
    {
        $virtualCurrency->delete();
        $virtualCurrency->rates()->delete();
        return redirect()->route('admin.virtual-currency.index')
            ->with('success', 'Currency deleted successfully');
    }
}
