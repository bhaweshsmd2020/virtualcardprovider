<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CardCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:card-category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                'url' => '#',
                'target' => '#addNewCategoryDrawer',
            ]
        ];
        $categories = Category::whereType('card_category')->latest()
            ->filterOn(['title', 'status'])
            ->paginate(10);

        $totalCategories = Category::whereType('card_category')->count();
        $activeCategories = Category::whereType('card_category')->where('status', 1)->count();
        $inActiveCategories = Category::whereType('card_category')->where('status', 0)->count();
        $languages = get_option('languages');

        return Inertia::render('Admin/CardCategory/Index', [
            'categories' => $categories,
            'totalCategories' => $totalCategories,
            'activeCategories' => $activeCategories,
            'inActiveCategories' => $inActiveCategories,
            'languages' => $languages,
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
            'title' => ['required', 'min:2', 'max:100'],
        ]);

        Category::create([
            'title' => $request->title,
            'status' => $request->status,
            'lang' => 'en',
            'type' => 'card_category',
            'slug' => Str::slug($request->title),
        ]);

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
            'title' => ['required', 'min:2', 'max:100'],
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'title' => $request->title,
            'status' => $request->status,
            'slug' => Str::slug($request->title),
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back();
    }
}
