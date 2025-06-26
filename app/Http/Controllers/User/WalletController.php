<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'virtual_currency_id' => 'required|numeric',
        ]);

        $wallet = Wallet::where('user_id', auth()->id())
            ->where('virtual_currency_id', $request->virtual_currency_id)
            ->first();
        if ($wallet) {
            throw ValidationException::withMessages([
                'virtual_currency_id' => 'Wallet already exists',
            ]);
        }

        $validatedData['user_id'] = auth()->id();
        Wallet::create($validatedData);

        return redirect()->back()->with('success', 'Wallet created successfully');
    }
}
