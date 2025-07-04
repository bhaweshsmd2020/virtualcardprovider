<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Traits\Uploader;

class ProjectController extends Controller
{
    use Uploader;

    public function __construct()
    {
        $this->middleware('permission:projects');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                'url' => route('admin.projects.create'),
              
            ]
        ];

        $projects = Project::with('category')
            ->filterOn(['title', 'is_active', 'is_featured'])
            ->filterOnRelation(['category_title'])
            ->paginate();
        $total = Project::count();
        $active = Project::where('is_active', 1)->count();
        $inActive = Project::whereNot('is_active', 1)->count();

        return inertia('Admin/Projects/Index', [
            'projects' => $projects,
            'total' => $total,
            'active' => $active,
            'inActive' => $inActive,
            'buttons' => $buttons,
            'segments' => $segments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereType('project')->get();
        return inertia('Admin/Projects/Create', [
            'categories' => $categories,
            'segments' => request()->segments(),
            'buttons' => [
                [
                    'name' => '<i class="fa fa-list"></i>&nbsp' . __('Back to list'),
                    'url' => route('admin.projects.index'),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $newProject = $request->validated();
        $newProject['meta'] = $request->input('seo');
        Project::create($newProject);
        return to_route('admin.projects.index');
    }

    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return inertia('Admin/Projects/Edit', [
            'project' => $project,
            'categories' => Category::whereType('project')->get(),
            'segments' => request()->segments(),
            'buttons' => [
                [
                    'name' => '<i class="fa fa-list"></i>&nbsp' . __('Back to list'),
                    'url' => route('admin.projects.index'),
                ]
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $modifiedProject = $request->validated();
        $modifiedProject['meta'] = $request->input('seo');
        $project->update($modifiedProject);
        return to_route('admin.projects.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        $this->unlinkPublicFile($project->preview);
        $this->unlinkPublicFile($project->banner_preview);
        return to_route('admin.projects.index');
    }
}
