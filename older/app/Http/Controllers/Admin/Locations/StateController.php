<?php

namespace App\Http\Controllers\Admin\Locations;

use App\Helpers\PageHeader;
use App\Helpers\Toastr;
use App\Models\State;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStateRequest;
use App\Http\Requests\UpdateStateRequest;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;

class StateController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        PageHeader::set()->title('States')->buttons([
            [
                'name' => __('Add New State'),
                'target' => "#addNewItemDrawer",
            ]
        ]);

        $states = State::query()
            ->with('country')
            ->when(
                in_array(request('type'), ['name', 'status']),
                fn($query) => $query->where(request('type'), 'LIKE', '%' . request('search') . '%')
            )
            ->when(
                request('type') == 'country_name',
                fn(Builder $query2) => $query2->whereHas(
                    'country',
                    fn(Builder $query3) => $query3->where('name', 'LIKE', '%' . request('search') . '%')
                )
            )
            ->paginate();

        $countries = Country::all();

        $counter = [
            'total' => State::count(),
            'active' => State::query()->active()->count(),
            'inactive' => State::query()->inactive()->count(),
        ];

        return Inertia::render('Admin/Locations/States/Index', compact('states', 'countries', 'counter'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStateRequest $request)
    {
        State::query()->create($request->validated());
        Toastr::success(__('State added successfully'));
        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateRequest $request, State $state)
    {
        $state->update($request->validated());
        Toastr::success(__('State updated successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();
        Toastr::success(__('State deleted successfully'));
        return back();
    }
}
