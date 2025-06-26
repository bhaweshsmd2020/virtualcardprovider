<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Traits\Uploader;
use Inertia\Inertia;

class PartnerController extends Controller
{
    use Uploader;

    public function __construct()
    {
        $this->middleware('permission:partner');
    }

    public function index()
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Create a partner'),
                'url' => '#',
                'target' => '#addNewPartnerDrawer',
            ]
        ];
        $brands = Category::whereType('partner')->latest()->paginate(10);
        $totalBrands = Category::whereType('partner')->count();
        $activeBrands = Category::whereType('partner')->where('status', 1)->count();
        $inActiveBrands = Category::whereType('partner')->where('status', 0)->count();

        return Inertia::render('Admin/Brand/Index', [
            'brands' => $brands,
            'totalBrands' => $totalBrands,
            'activeBrands' => $activeBrands,
            'inActiveBrands' => $inActiveBrands,
            'buttons' => $buttons,
            'segments' => $segments,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'url' => ['max:100'],
            'preview' => ['required', 'image', 'max:1024'],
        ]);

        Category::create([
            'title' => $request->url ?? '#',
            'status' => $request->status,
            'lang' => 'en',
            'type' => 'partner',
            'preview' => $request->preview,
        ]);

        Toastr::success(__('Added Successfully'));

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['max:100'],
            'preview' => ['nullable', 'image', 'max:1024'],
        ]);

        $brand = Category::where('type', 'partner')->findOrFail($id);

        $brand->update([
            'title' => $request->title ?? '#',
            'status' => $request->status,
            'type' => $request->type,
            'preview' => $request->preview,
        ]);

        Toastr::success(__('Updated Successfully'));

        return redirect()->route('admin.partner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Category::findOrFail($id);
        $this->removeFile($brand->preview);
        $brand->delete();

        Toastr::danger(__('Deleted Successfully'));

        return redirect()->back();
    }
}
