<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PageHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Models\Card;
use App\Models\CardEmail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardemailController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:cards');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emails = CardEmail::with(['user'])->filterOn(['email', 'status'])->paginate(10);
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp' . __('Add New'),
                'url' => '#',
                'target' => '#addNewEmailDrawer',
            ]
        ];
        return Inertia::render('Admin/CardEmail/Index', [
            'emails' => $emails,
            'buttons' => $buttons
        ]);
    }  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'min:5', 'max:100'],
        ]);

        CardEmail::create([
            'email' => $request->email
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => ['required', 'min:5', 'max:100'],
        ]);

        $category = CardEmail::findOrFail($id);

        $category->update([
            'email' => $request->email,
            'status' => $request->status
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = CardEmail::findOrFail($id);
        $category->delete();

        return redirect()->back();
    }
}
