<?php

namespace App\Http\Controllers\Admin;

use App\Models\PayoutMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\VirtualCurrency;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Traits\Uploader;

class PayoutMethodController extends Controller
{
    use Uploader;
    public function __construct()
    {
        $this->middleware('permission:payout-methods');
    }

    public function index()
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Create Payout Method'),
                'url' => route('admin.payout-methods.create'),
            ]
        ];
        $methods = PayoutMethod::latest()
            ->when(request('type'), function ($q) {
                return $q->where(request('type'), 'like', '%' . request('search') . '%');
            })
            ->paginate(20);
        $totalPayoutMethod =  PayoutMethod::count();
        $totalActivePayoutMethod =  PayoutMethod::where('status', 1)->count();
        $totalInActivePayoutMethod =  PayoutMethod::where('status', 0)->count();

        return Inertia::render('Admin/PayoutMethod/Index', [
            'methods' => $methods,
            'buttons' => $buttons,
            'segments' => $segments,
            'totalPayoutMethod' => $totalPayoutMethod,
            'totalActivePayoutMethod' => $totalActivePayoutMethod,
            'totalInActivePayoutMethod' => $totalInActivePayoutMethod,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => __('Back'),
                'url' => route('admin.payout-methods.index'),
            ]
        ];
        $currencies = VirtualCurrency::all()->pluck('currency');
        return Inertia::render('Admin/PayoutMethod/Create', [
            'buttons' => $buttons,
            'segments' => $segments,
            'currencies' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'currency_name' => ['required', 'string'],
            'charge_type' => ['required', 'string'],
            'delay' => ['required', 'numeric'],
            'image'  => 'required|image|max:2000',
            'min_limit' => ['required', 'gt:0'],
            'max_limit' => ['required', 'after_or_equal:min_limit'],
            'fixed_charge' => ['nullable', 'gt:0', 'min:1'],
            'percent_charge' => ['nullable', 'between:0,100', 'min:1'],
            'instruction' => ['required', 'string'],
            'status' => ['required'],
            'rates' => 'nullable|array',
        ]);

        $preview = $this->saveFile($request, 'image');
        $data = json_encode($request->inputs);

        $method = new PayoutMethod;
        $method->name = $request->name;
        $method->currency_name = $request->currency_name;
        $method->charge_type = $request->charge_type;
        $method->delay = $request->delay;
        $method->min_limit = $request->min_limit;
        $method->max_limit = $request->max_limit;
        $method->fixed_charge = $request->fixed_charge;
        $method->percent_charge = $request->percent_charge;
        $method->instruction = $request->instruction;
        $method->status = $request->status;
        $method->image = $preview;
        $method->data = $data;
        $method->rates = $request->rates;
        $method->save();

        return to_route('admin.payout-methods.index');
    }

    public function edit($id)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => __('Back'),
                'url' => route('admin.payout-methods.index'),
            ]
        ];
        $payoutMethod = PayoutMethod::find($id);
        $currencies = VirtualCurrency::all()->pluck('currency');
        return Inertia::render('Admin/PayoutMethod/Edit', [
            'segments' => $segments,
            'buttons' => $buttons,
            'payoutMethod' => $payoutMethod,
            'currencies' => $currencies
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payoutMethod.name' => ['required', 'string'],
            'payoutMethod.delay' => ['required', 'numeric'],
            'payoutMethod.currency_name' => ['required', 'string'],
            'payoutMethod.min_limit' => ['required', 'gt:0'],
            'payoutMethod.charge_type' => ['required', 'string'],
            'payoutMethod.max_limit' => ['required', 'after_or_equal:min_limit'],
            'payoutMethod.fixed_charge' => ['nullable', 'gt:0'],
            'payoutMethod.percent_charge' => ['nullable', 'between:0,100'],
            'payoutMethod.instruction' => ['required', 'string'],
            'payoutMethod.image'  => 'nullable|image|max:2000',
            'payoutMethod.status' => ['required'],
            'payoutMethod.rates' => 'nullable|array',
        ]);


        $data = json_encode($request['payoutMethod']['data']) ?? null;

        $request = $request['payoutMethod'];

        $method =  PayoutMethod::findOrFail($id);
        $method->name = $request['name'];
        $method->currency_name = $request['currency_name'];
        $method->charge_type = $request['charge_type'];
        $method->delay = $request['delay'];
        $method->min_limit = $request['min_limit'];
        $method->max_limit = $request['max_limit'];
        $method->fixed_charge = $request['fixed_charge'];
        $method->percent_charge = $request['percent_charge'];
        $method->instruction = $request['instruction'];
        $method->status = $request['status'];
        $method->rates = $request['rates'];
        $method->data = $data;

        if (request()->hasFile('payoutMethod.image')) {
            $method->image = $this->saveFile(request(), 'payoutMethod.image');
        }
        $method->save();

        return to_route('admin.payout-methods.index');
    }

    public function destroy($id)
    {
        $method = PayoutMethod::find($id);
        $this->removeFile($method->image);
        $method->delete();
        return back();
    }

    public function deleteAll(Request $request)
    {
        foreach ($request->ids as $id) {
            $method = PayoutMethod::find($id);
            if (file_exists($method->image)) {
                $this->removeFile($method->image);
            }
            $method->delete();
        }
        return back();
    }
}
