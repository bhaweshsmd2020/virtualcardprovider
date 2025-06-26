<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Inertia\Inertia;
use App\Helpers\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:menu');
    }

    public function index()
    {
        $menus = Menu::latest()->get();
        $languages = get_option('languages');

        $totalMenus = Menu::count();
        $totalActiveMenus = Menu::where('status', 1)->count();
        $totalDraftMenus = Menu::where('status', 0)->count();

        $segments = request()->segments();

        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Create Menu'),
                'url' => '#',
                'target' => '#addNewMenuDrawer',
            ]
        ];

        return Inertia::render('Admin/Menu/Index', compact('menus', 'languages', 'totalMenus', 'totalActiveMenus', 'totalDraftMenus', 'segments', 'buttons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'position' => ['required'],
            'status' => ['required'],
            'language' => ['required'],
        ]);

        if ($request->status == 1) {
            Menu::where('position', $request->position)
                ->where('lang', $request->lang)
                ->update(['status' => 0]);
        }

        $men = new Menu;
        $men->name = $request->name;
        $men->position = $request->position;
        $men->status = $request->status;
        $men->lang = $request->language;
        $men->data = "[]";
        $men->save();

        Toastr::success(__('Menu Created Successfully.'));

        return back();
    }

    public function updateData($id, Request $request)
    {
        $info = Menu::findOrFail($id);
        $info->data = $request->data;
        $info->save();

        Cache::flush();

        return response()->json([
            'message' => __('Menu Updated Successfully.')
        ]);
    }

    public function show($id)
    {
        $info = Menu::findOrFail($id);
        $contents = is_array($info->data) ? $info->data : [];

        $segments = request()->segments();

        $buttons = [
            [
                'name' => __('Back'),
                'url' => route('admin.menu.index')
            ]
        ];
        return Inertia::render('Admin/Menu/Show', [
            'info' => $info,
            'contents' => $contents,
            'segments' => $segments,
            'buttons' => $buttons
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate form data
        $request->validate([
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',
            'language' => 'required',
        ]);

        // If menu is active, deactivate all others from same position and lang
        if ($request->status == 1) {
            Menu::where('position', $request->position)
                ->where('lang', $request->lang)
                ->update(['status' => 0]);
        }

        // Update menu
        $menu = Menu::find($id);
        $menu->name = $request->name;
        $menu->position = $request->position;
        $menu->status = $request->status;
        $menu->lang = $request->language;
        $menu->save();

        Cache::flush();

        // Success message
        Toastr::success(__('Menu Updated Successfully.'));

        // Return back
        return back();
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        // Clear cache for current position and lang
        Cache::flush();
        Toastr::success(__('Menu Deleted Successfully.'));
        return back();
    }
}
