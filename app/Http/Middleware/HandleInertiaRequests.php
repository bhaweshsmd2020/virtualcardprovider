<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Inertia\Middleware;
use App\Helpers\SeoMeta;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Helpers\PageHeader;
use App\Helpers\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class HandleInertiaRequests extends Middleware
{
    protected $request;
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    // protected $rootView = 'app';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function rootView(Request $request)
    {
        $segments = $request->segments();
        $segment = $segments[0] ?? null;
        $layoutPath = 'layouts.' . $this->getLayoutName($segment);
        return $layoutPath;
    }

    private function getLayoutName($segment): string
    {
        return match ($segment) {
            'admin' => 'admin',
            'user' => 'admin',
            default => 'default',
        };
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        if (request()->is('install/*') || !file_exists(base_path('public/uploads/installed'))) {
            return [];
        }
        $primaryData = get_option('primary_data', true);
        $locale = Session::get('locale', env('DEFAULT_LANG', 'en'));
        $menu = Cache::remember(
            'menu_web_' . $locale,
            env('CACHE_LIFETIME', 1800),
            function () use ($locale) {
                return Menu::where('status', 1)
                    ->where('location', 'web')
                    ->where('lang', $locale)->oldest()->get();
            }
        );

        $app_name = Cache::remember(
            'app_name_' . $locale,
            env('CACHE_LIFETIME', 1800),
            function () {
                return env('APP_NAME', 'Laravel');
            }
        );

        $permissions = [];
        $userNotifications = [];

        if (auth()->check()) {
            /**
             * @var \App\Models\User
             */
            $user = auth()->user();

            // permissions
            if ($user->isAdmin() && $this->request->is('admin/*')) {
                $permissions = $user->getAllPermissions()->pluck('name');
            }

            if ($this->request->is('admin/*') || $this->request->is('user/*')) {
                // notifications
                $userNotifications = Notification::query()
                    ->when($user->role != 'admin', fn($q) => $q->where('user_id', $user->id)->where('for_admin', 0))
                    ->when($user->role == 'admin', fn($q) => $q->where('for_admin', 1))
                    ->latest()
                    ->orderBy('seen')
                    ->limit(10)
                    ->get()
                    ->map(function ($item) {
                        $item->title_short = Str::limit($item->title, 30, '...');
                        $item->comment_short = Str::limit($item->comment, 35, '...');
                        return $item;
                    }) ?? [];
            }

        }

        return array_merge(parent::share($request), [

            // only auth
            'user' => auth()->check() ? auth()->user() : null,
            'userNotifications' => $userNotifications ?? [],

            // static values
            'languages' => get_option('languages'),
            'currency' => get_option('base_currency'),
            'primaryData' => $primaryData,
            'app_name' => $app_name,

            // if auth
            'permissions' => $permissions,

            // dynamic values
            'locale' => session('locale', 'en'),
            'toast' => fn() => Toastr::Toast(),
            'pageHeader' => fn() => PageHeader::toArray(),
            'flash' => $request->session()->get('flash', []),

            // conditional required
            // Web
            'menus' => $menu,
            'seoMeta' => fn() => SeoMeta::get(),
            'newsletter_api' => fn() => env('NEWSLETTER_API_KEY', null),
        ]);
    }
}
