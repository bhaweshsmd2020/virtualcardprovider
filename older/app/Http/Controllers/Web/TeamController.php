<?php

namespace App\Http\Controllers\Web;

use App\Models\Post;
use Inertia\Inertia;
use App\Helpers\SeoMeta;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index()
    {
        SeoMeta::init('seo_team');

        $teams = Post::where('type', 'team')
            ->with('preview', 'excerpt')
            ->where('status', 1)
            ->latest()
            ->get();
        return Inertia::render('Web/Team/Index', [
            'teams' => $teams
        ]);
    }
    public function show($id)
    {
        $team = Post::with('preview', 'excerpt', 'info', 'description')->where('type','team')->where('status',1)->findOrFail($id);
        $team->social_medias = collect(json_decode($team->excerpt->value ?? '{}', true))
            ->map(function ($item, $key) {
                return [
                    'name' => $key,
                    'url' => $item
                ];
            })->values()->all();

        SeoMeta::set([
            'title' => $team->title,
            'description' => 'Team'
        ]);

        return Inertia::render('Web/Team/Show', [
            'team' => $team,
            'information' => json_decode($team->info?->value ?? '{}', true)
        ]);
    }
}
