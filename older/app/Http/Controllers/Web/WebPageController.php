<?php

namespace App\Http\Controllers\Web;

use App\Models\Plan;
use App\Models\Post;
use Inertia\Inertia;
use App\Models\Category;
use App\Helpers\SeoMeta;
use App\Http\Controllers\Controller;

class WebPageController extends Controller
{

    public function home()
    {
        if (!file_exists(base_path('public/uploads/installed'))) {
          return redirect('/install');
        }
        SeoMeta::init('seo_home');

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

        $faqs = Post::where('type', 'faq')
            ->with(['excerpt', 'faq_categories:id,title'])
            ->latest()
            ->where('lang', current_locale())
            ->get()
            ->map(function ($item) {
                return [
                    'question' => $item->title,
                    'answer' => $item->excerpt?->value ?? '',
                ];
            });

        $logos = Category::whereType('partner')->latest()->pluck('preview');

        $blogs = Post::where('type', 'blog')
            ->with(['excerpt', 'preview', 'categories:id,title'])->latest()
            ->limit(2)
            ->get();

        return Inertia::render('Web/Home/Index', [
            'home' => get_option('home_page', true),
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'blogs' => $blogs,
            'logos' => $logos
        ]);
    }

    public function about()
    {
        SeoMeta::init('seo_about');
        $about = get_option('about_page', true);

        $logos = Category::whereType('partner')->where('status', 1)->latest()->pluck('preview');
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
            ->where('lang', current_locale())
            ->get()
            ->map(function ($item) {
                return [
                    'question' => $item->title,
                    'answer' => $item->excerpt?->value ?? '',
                ];
            });

        $blogs = Post::where('type', 'blog')
            ->with(['excerpt', 'preview', 'categories:id,title'])->latest()
            ->limit(2)
            ->get();


        return Inertia::render('Web/About/Index', [
            'about' => $about,
            'testimonials' => $testimonials,
            'logos' => $logos,
            'faqs' => $faqs,
            'blogs' => $blogs,
        ]);
    }


    public function projects()
    {
        SeoMeta::init('seo_projects');
        return Inertia::render('Web/Projects/Index', []);
    }


    public function pricing()
    {
        SeoMeta::init('seo_pricing');

        $plans = Plan::query()
            ->where('status', 1)
            ->orderBy('price', 'asc')
            ->where('is_default', 0)
            ->get();

        $about = get_option('about_page', true);
        $home = get_option('home_page', true);
        $brands = Category::whereType('partner')->latest()->get();

        $faqs = Post::where('type', 'faq')->with(['excerpt', 'faq_categories:id,title'])
            ->latest()
            ->get();

        $testimonials = Post::where('type', 'testimonial')->with(['excerpt', 'preview'])->limit(6)->get();

        $planSettings = get_option('plan');
        return Inertia::render('Web/Pricing', [
            'plans' => $plans,
            'home' => $home,
            'about' => $about,
            'brands' => $brands,
            'faqs' => $faqs,
            'testimonials' => $testimonials,
            'planSettings' => $planSettings,
        ]);
    }

    public function faqs()
    {
        SeoMeta::init('seo_faq');

        $categories = Category::where('status', 1)
            ->where('type', 'faq_category')
            ->with('faqs.excerpt')
            ->latest()
            ->get();

        return Inertia::render('Web/Faq', compact('categories'));
    }

    public function callback ()
    {
        return true;
    }

    public function page($slug)
    {
        $customPage = Post::with('description', 'seo')->where(['slug' => $slug, 'status' => 1])->firstOrFail();

        $seo = (array) json_decode($customPage->seo?->value ?? []);

        SeoMeta::set($seo);

        return Inertia::render('Web/CustomPage', compact('customPage'));
    }

    public function whitelabel()
    {
        SeoMeta::init('seo_whitelabel');
        $about = get_option('white_label_page', true);

        $logos = Category::whereType('partner')->where('status', 1)->latest()->pluck('preview');
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
            ->where('lang', current_locale())
            ->get()
            ->map(function ($item) {
                return [
                    'question' => $item->title,
                    'answer' => $item->excerpt?->value ?? '',
                ];
            });

        $blogs = Post::where('type', 'blog')
            ->with(['excerpt', 'preview', 'categories:id,title'])->latest()
            ->limit(2)
            ->get();


        return Inertia::render('Web/WhiteLabel/Index', [
            'about' => $about,
            'testimonials' => $testimonials,
            'logos' => $logos,
            'faqs' => $faqs,
            'blogs' => $blogs,
        ]);
    }
}
