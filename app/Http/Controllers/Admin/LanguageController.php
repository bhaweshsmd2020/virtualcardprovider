<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Inertia\Inertia;
use App\Models\Option;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:language');
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
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Create a language'),
                'url' => '#',
                'target' => '#addNewLanguageDrawer',
            ]
        ];
        $languages = get_option('languages');
        $countries = base_path('lang/langlist.json');
        $countries = json_decode(file_get_contents($countries), true);

        return Inertia::render('Admin/Language/Index', [
            'languages' => $languages,
            'countries' => $countries,
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
        $file = base_path('lang/default.json');
        $file = file_get_contents($file);
        File::put(base_path('lang/' . $request->language_code . '.json'), $file);
        $languages = get_option('languages');

        $arr = [];
        if (!empty($languages)) {
            foreach ($languages as $key => $value) {
                $arr[$key] = $value;
            }
        }

        $arr[$request->language_code] = $request->name;

        $langlist = Option::where('key', 'languages')->first();
        if (empty($langlist)) {
            $langlist = new Option;
            $langlist->key = 'languages';
        }
        $langlist->value = $arr;
        $langlist->save();
        Cache::forget('languages');

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $segments = request()->segments();
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add Translation Key'),
                'url' => '#',
                'target' => '#addNewLanguageKeyDrawer',
            ],
            [
                'name' => __('Back'),
                'url' => route('admin.language.index'),
            ]
        ];
        $file = base_path('lang/' . $id . '.json');
        $posts = file_get_contents($file);
        $posts = json_decode($posts);

        return Inertia::render('Admin/Language/Show', [
            'posts' => $posts,
            'id' => $id,
            'buttons' => $buttons,
            'segments' => $segments,
        ]);
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
        $data = [];
        foreach ($request->values as $key => $row) {
            $data[$key] = $row;
        }

        $file = json_encode($data, JSON_PRETTY_PRINT);
        File::put(base_path('lang/' . $id . '.json'), $file);

        return Inertia::location(back());
    }

    public function addKey(Request $request)
    {

        $request->validate([
            'key'  => 'required',
            'value' => 'required',
        ]);

        $file = base_path('lang/' . $request->id . '.json');
        $posts = file_get_contents($file);
        $posts = json_decode($posts);
        foreach ($posts as $key => $row) {
            $data[$key] = $row;
        }
        $data[$request->key] = $request->value;

        File::put(base_path('lang/' . $request->id . '.json'), json_encode($data, JSON_PRETTY_PRINT));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $posts = Option::where('key', 'languages')->first();
        $languages = $posts->value;

        $data = [];
        foreach ($languages as $key => $row) {
            if ($id != $key) {
                $data[$key] = $row;
            }
        }

        $posts->value = $data;
        $posts->save();

        if (file_exists(base_path('lang/' . $id . '.json'))) {
            unlink(base_path('lang/' . $id . '.json'));
        }

        Cache::forget('languages');

        return back();
    }
}
