<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Project;
use App\Helpers\SeoMeta;
use Inertia\Inertia;

;

class ProjectController extends Controller
{
    public function index()
    {
        SeoMeta::init('seo_projects');

        return Inertia::render('Web/Projects/Index', [
            'projects' => Project::query()->with('category')->where('is_active',1)->paginate(10),
            'categories' => Category::query()->whereType('project')->get(['id', 'title'])
        ]);
    }

    public function show(Project $project)
    {
        $project->load('category');

        SeoMeta::set($project->meta);
        return Inertia::render('Web/Projects/Show', [
            'project' => $project,
            'nextProject' => Project::query()->where('id', '>', $project->id)->where('is_active',1)->first() ?? new Project,
            'prevProject' => Project::query()->where('id', '<', $project->id)->where('is_active',1)->orderBy('id', 'desc')->first() ?? new Project
        ]);
    }
}
