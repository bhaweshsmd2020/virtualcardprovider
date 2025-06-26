<?php

namespace App\Http\Controllers\Admin\Locations;

use App\Helpers\PageHeader;
use App\Helpers\Toastr;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountryStoreRequest;
use App\Http\Requests\CountryUpdateRequest;
use Inertia\Inertia;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        PageHeader::set()->title('Countries')->buttons([
            [
                'name' => __('Add New Country'),
                'target' => "#addNewItemDrawer",
            ]
        ]);

        $countries = Country::query()
            ->when(
                in_array(request('type'), ['name', 'code', 'status']),
                fn($q) => $q->where(request('type'), 'LIKE', '%' . request('search') . '%')
            )
            ->paginate();

        $counter = [
            'total' => Country::count(),
            'active' => Country::query()->active()->count(),
            'inactive' => Country::query()->inactive()->count(),
        ];

        return Inertia::render('Admin/Locations/Countries/Index', compact('countries', 'counter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryStoreRequest $request)
    {
        Country::query()->create($request->validated());
        Toastr::success(__('Country added successfully'));
        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryUpdateRequest $request, Country $country)
    {
        $country->update($request->validated());
        Toastr::success(__('Country updated successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();
        Toastr::success(__('Country deleted successfully'));
        return back();
    }
}
