<?php

namespace App\Http\Controllers\Web;

use App\Models\Post;
use App\Models\Service;
use App\Helpers\SeoMeta;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        SeoMeta::init('seo_services');



        $featuredService = Service::query()
            ->with('category')
            ->where('is_active', 1)
            ->where('is_featured', 1)
            ->latest()
            ->first();

        if (request('page') > 1) {
            $featuredService = false;
        }

        $services = Service::query()
            ->with('category')
            ->where('is_active', 1)
            ->where('id', '!=', $featuredService?->id ?? 0)
            ->latest()
            ->paginate(10);

        $testimonials = Post::where('type', 'testimonial')->with(['excerpt', 'preview'])->latest()->get()
            ->map(function ($item) {
                return [
                    'image' => $item->preview?->value ?? '',
                    'name' => $item->title,
                    'designation' => $item->slug,
                    'content' => $item->excerpt?->value ?? '',
                    'rating' => intval($item->lang),
                ];
            });

        $faqs = Post::where('type', 'faq')->with(['excerpt', 'faq_categories:id,title'])
            ->latest()
            ->where('lang', App::getLocale())
            ->get();

        $logos = Category::whereType('partner')->latest()->pluck('preview');


        return inertia('Web/Services/Index', [
            'featuredService' => $featuredService,
            'services' => $services,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'service_page' => get_option('service_page', true),
            'home_page' => get_option('home_page', true),
            'logos' => $logos
        ]);
    }

    public function categoryShow($slug)
    {
        SeoMeta::init('seo_services');

        $category = Category::where('slug', $slug)->where('status', 1)->firstOrFail();
        $services = $category->services()->where('is_active', 1)->latest()->paginate(10);
        $categories = Category::where('status', 1)->where('type', 'service')->latest()->get();

        return Inertia::render('Web/Services/Categories/Index', [
            'slug' => $slug,
            'category' => $category,
            'categories' => $categories,
            'services' => $services,
            'service_page' => get_option('service_page', true),
        ]);
    }

    public function show(Service $service)
    {
        SeoMeta::set($service->meta);
        $categories = Category::where('status', 1)->where('type', 'service')->latest()->get();
        $servicePage = get_option('service_page', true);
        $testimonials = Post::where('type', 'testimonial')->with(['excerpt', 'preview'])->limit(6)->get();

        $service->load('category');

        return Inertia::render('Web/Services/Show', [
            'service' => $service,
            'testimonials' => $testimonials,
            'categories' => $categories,
            'servicePage' => $servicePage
        ]);
    }
}
