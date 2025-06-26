<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PageHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Models\Card;
use App\Models\User;
use App\Models\CardlimitRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\CardHelper;

class LimitrequestController extends Controller
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
        $limits = CardlimitRequest::with(['user', 'credit_card'])->filterOn(['status'])->paginate(10);
        return Inertia::render('Admin/CardRequest/Index', [
            'limits' => $limits
        ]);
    }  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {        
        $request->validate([
            'status' => ['required'],
        ]);

        $limit = CardlimitRequest::findOrFail($id);
        $user = User::findOrFail($limit->user_id);
        if($user->balance < $limit->total){
            return back()->with('warning', 'Insufficient Balance');
        }

        if($request->status == 2){
            return back()->with('warning', 'Request rejected successfully');
        }

        if($request->status == 0){
            return back()->with('warning', 'Request updated successfully');
        }

        $cardHelper = new CardHelper();
        $cardUsesLimit = $cardHelper->cardUsesLimit($id);
        
        $user->update([
            'balance' => $user->balance - $limit->total
        ]);

        $limit->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Request approved successfully');
    }
}
