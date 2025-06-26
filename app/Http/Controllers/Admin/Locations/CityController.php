<?php

namespace App\Http\Controllers\Admin\Locations;

use App\Helpers\PageHeader;
use App\Helpers\Toastr;
use App\Models\City;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\Country;
use Inertia\Inertia;

class CityController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        PageHeader::set()->title('Cities')->buttons([
            [
                'name' => __('Add New City'),
                'target' => "#addNewItemDrawer",
            ]
        ]);

        $cities = City::query()
            ->with(['state', 'country'])
            ->filterOn(['name', 'postal_code', 'status'])
            ->filterOnRelation(['state_name', 'country_name'])
            ->paginate();

        $countries = Country::all();

        $counter = [
            'total' => City::count(),
            'active' => City::query()->active()->count(),
            'inactive' => City::query()->inactive()->count(),
        ];

        return Inertia::render('Admin/Locations/Cities/Index', compact('cities', 'countries', 'counter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        City::query()->create($request->validated());
        Toastr::success(__('City added successfully'));
        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        $city->update($request->validated());
        Toastr::success(__('City updated successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        $city->delete();
        Toastr::success(__('City deleted successfully'));
        return back();
    }
}
