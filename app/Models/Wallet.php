<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory, UUID;

    protected $guarded = [];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(VirtualCurrency::class, 'virtual_currency_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
