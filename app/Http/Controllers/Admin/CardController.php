<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PageHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Models\Card;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardController extends Controller
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
        PageHeader::set()->title('Cards')
            ->buttons([
                ['name' => 'New Card', 'url' => route('admin.cards.create')],
                [
                    'name' => '<i class="bx bx-file"></i>&nbsp&nbsp' . __('Card Intro Details'),
                    'url' => '#',
                    'target' => '#cardIntroDrawer',
                ],
            ]);



        $cards = Card::with('category')
            ->withCount('card_orders')
            ->withCount([
                'credit_cards' => function ($query) {
                    $query->where('status', 'active');
                }
            ])
            ->filterOn(['title', 'status', 'is_featured'])
            ->filterOnRelation(['category_title'])
            ->paginate();


        $card_intro_details = get_option('card_intro_details');

        $totalCard = Card::count();
        $totalActiveCard = Card::where('status', 'active')->count();
        $totalInactiveCard = Card::where('status', 'inactive')->count();
        return Inertia::render('Admin/Card/Index', [
            'cards' => $cards,
            'card_intro_details' => $card_intro_details,
            'totalCard' => $totalCard,
            'totalActiveCard' => $totalActiveCard,
            'totalInactiveCard' => $totalInactiveCard,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        PageHeader::set()->title('Card Create')
            ->buttons([
                ['name' => 'Back', 'url' => route('admin.cards.index')]
            ]);
        $merchantCategories = json_decode(file_get_contents(database_path('json/merchant_categories.json')));
        $categories = Category::whereType('card_category')->where('status', 1)->get();

        return Inertia::render('Admin/Card/Create', [
            'merchantCategories' => $merchantCategories,
            'categories' => $categories,
            'card_variants' => Card::CARD_VARIANTS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCardRequest $request)
    {
        Card::create($request->validated());

        return redirect()->route('admin.cards.index')->with('success', 'Card created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        return Inertia::render('Admin/Card/Show', ['card' => $card]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Card $card)
    {
        $merchantCategories = json_decode(file_get_contents(database_path('json/merchant_categories.json')));
        $categories = Category::whereType('card_category')->where('status', 1)->get();
        return Inertia::render('Admin/Card/Edit', [
            'card' => $card,
            'merchantCategories' => $merchantCategories,
            'categories' => $categories,
            'card_variants' => Card::CARD_VARIANTS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardRequest $request, Card $card)
    {
        $card->update($request->validated());

        return redirect()->route('admin.cards.index')
            ->with('warning', 'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $card = Card::findOrFail($id);
        $card->loadCount([
            'credit_cards' => function ($query) {
                $query->where('status', 'active');
            }
        ]);

        if ($card->credit_cards_count > 0) {
            return redirect()->back()->with('warning', 'Card has active prepaid cards');
        }

        $card->delete();
        return redirect()->route('admin.cards.index')
            ->with('danger', 'Deleted Successfully');
    }
}
