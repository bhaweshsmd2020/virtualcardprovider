<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Option;
use App\Traits\Uploader;
use Illuminate\Http\Request;
use App\Actions\OptionUpdate;
use App\Helpers\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    use Uploader;

    public function __construct()
    {
        $this->middleware('permission:page-settings');
    }

    public function index()
    {
        Cache::flush();

        $primary_data = get_option('primary_data', true);
        $home_page = get_option('home_page', true);
        $about_page = get_option('about_page', true);
        $contact_page = get_option('contact_page', true);
        $auth_pages = get_option('auth_pages', true);
        $service_page = get_option('service_page', true);

        // Ensure home_page is an array
        if (is_null($home_page)) {
            \Log::warning('home_page is null, setting default');
            $home_page = [];
        } elseif (is_string($home_page)) {
            \Log::warning('home_page is a string, attempting to decode JSON', ['home_page' => $home_page]);
            $home_page = json_decode($home_page, true) ?? [];
        }
        if (!is_array($home_page)) {
            \Log::error('home_page is not an array, setting default', ['home_page' => $home_page]);
            $home_page = [];
        }

        // Initialize business_payments if not exists
        if (!isset($home_page['business_payments'])) {
            \Log::info('business_payments missing, initializing default');
            $home_page['business_payments'] = [
                'title' => 'Stay on top of every business payment',
                'subtitle' => '',
                'cards' => []
            ];
        }

        // Ensure cards is an array
        if (!isset($home_page['business_payments']['cards']) || !is_array($home_page['business_payments']['cards'])) {
            \Log::info('business_payments.cards missing or not an array, initializing default');
            $home_page['business_payments']['cards'] = [
                [
                    'image' => '',
                    'title' => '',
                    'description' => '',
                    'badges_string' => ''
                ]
            ];
        }

        // Sanitize business_payments.cards
        foreach ($home_page['business_payments']['cards'] as &$card) {
            if (!is_array($card)) {
                \Log::warning('business_payments card is not an array, resetting', ['card' => $card]);
                $card = [];
            }
            $card['image'] = isset($card['image']) && is_string($card['image']) ? $card['image'] : '';
            $card['title'] = isset($card['title']) && is_string($card['title']) ? $card['title'] : '';
            $card['description'] = isset($card['description']) && is_string($card['description']) ? $card['description'] : '';
            $card['badges_string'] = isset($card['badges_string']) && is_string($card['badges_string']) ? $card['badges_string'] : '';
        }

        // Log data sent to frontend
        \Log::info('Data sent to frontend', ['home_page' => $home_page]);

        return Inertia::render('Admin/PageSetting/Index', [
            'primary_data' => $primary_data,
            'home_page' => $home_page,
            'about_page' => $about_page,
            'contact_page' => $contact_page,
            'auth_pages' => $auth_pages,
            'service_page' => $service_page,
        ]);
    }

    public function update($id)
    {
        Cache::flush();
        // Enhanced logging to capture file inputs
        \Log::info('Update Request Data', [
            'all' => request()->all(),
            'files' => request()->file()
        ]);
        $optionUpdate = new OptionUpdate();
        $optionUpdate->update($id);
        Toastr::success(__('Information has been updated.'));
        return back();
    }
}