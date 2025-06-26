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

        $optionUpdate = new OptionUpdate();
        $optionUpdate->update($id);
        Toastr::success(__('Information has been updated.'));
        return back();
    }
}
