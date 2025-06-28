<?php

namespace App\Http\Controllers\Web;

use App\Models\Plan;
use App\Models\Post;
use Inertia\Inertia;
use App\Models\Category;
use App\Helpers\SeoMeta;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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

        $home_page = get_option('home_page', true);

        // Log raw home_page data
        Log::info('Raw home_page data', ['home_page' => $home_page]);

        // Ensure home_page is an array
        if (is_null($home_page)) {
            Log::warning('home_page is null, setting default');
            $home_page = [];
        } elseif (is_string($home_page)) {
            Log::warning('home_page is a string, attempting to decode JSON', ['home_page' => $home_page]);
            $home_page = json_decode($home_page, true) ?? [];
        }
        if (!is_array($home_page)) {
            Log::error('home_page is not an array, setting default', ['home_page' => $home_page]);
            $home_page = [];
        }

        // Initialize business_payments if not exists
        if (!isset($home_page['business_payments'])) {
            Log::info('business_payments missing, initializing default');
            $home_page['business_payments'] = [
                'title' => 'Stay on top of every business payment',
                'subtitle' => '',
                'cards' => []
            ];
        }

        // Ensure cards is an array
        if (!isset($home_page['business_payments']['cards']) || !is_array($home_page['business_payments']['cards'])) {
            Log::info('business_payments.cards missing or not an array, initializing default');
            $home_page['business_payments']['cards'] = [];
        }

        // Sanitize business_payments.cards
        foreach ($home_page['business_payments']['cards'] as &$card) {
            $card['image'] = isset($card['image']) && is_string($card['image']) && $card['image'] ? $card['image'] : '/assets/images/default-image.png';
            $card['title'] = $card['title'] ?? '';
            $card['description'] = $card['description'] ?? '';
            $card['badges'] = isset($card['badges']) && is_array($card['badges']) ? $card['badges'] : [];
            $card['badges_string'] = isset($card['badges_string']) && is_string($card['badges_string']) ? $card['badges_string'] : (!empty($card['badges']) ? implode(', ', $card['badges']) : '');
        }

        // Log sanitized home_page data
        Log::info('Sanitized home_page data', ['home_page' => $home_page]);

        return Inertia::render('Web/Home/Index', [
            'home' => $home_page ?: [],
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
