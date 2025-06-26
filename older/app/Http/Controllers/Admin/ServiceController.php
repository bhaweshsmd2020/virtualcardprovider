<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Category;
use App\Models\Service;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                'url' => route('admin.services.create'),
            ]
        ];

        $services = Service::with('category')
            ->filterOn(['title', 'overview', 'is_active', 'is_featured'])
            ->latest()->paginate();
        $total = Service::count();
        $active = Service::where('is_active', 1)->count();
        $inActive = Service::whereNot('is_active', 1)->count();

        return inertia('Admin/Services/Index', [
            'services' => $services,
            'total' => $total,
            'active' => $active,
            'inActive' => $inActive,
            'buttons' => $buttons,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereType('service')->get();
        return Inertia::render('Admin/Services/Create', [
            'categories' => $categories,
            'segments' => request()->segments(),
            'buttons' => [
                [
                    'name' => '<i class="fa fa-list"></i>&nbsp' . __('Back to list'),
                    'url' => route('admin.services.index'),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $newService = $request->validated();
        $newService['meta'] = $request->input('seo');
        Service::create($newService);
        return to_route('admin.services.index')->with('success', 'Saved successfully');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $categories = Category::whereType('service')->get();
        return inertia('Admin/Services/Edit', [
            'categories' => $categories,
            'service' => $service,
            'segments' => request()->segments(),
            'buttons' => [
                [
                    'name' => '<i class="fa fa-list"></i>&nbsp' . __('Back to list'),
                    'url' => route('admin.services.index'),
                ]
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $newService = $request->validated();
        $newService['meta'] = $request->input('seo');

        $service->update($newService);

        return to_route('admin.services.index')->with('info', 'Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return to_route('admin.services.index')->with('danger', 'Deleted successfully');
    }
}
