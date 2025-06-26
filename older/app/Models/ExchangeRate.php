<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(VirtualCurrency::class);
    }
    public function exchange_currency(): BelongsTo
    {
        return $this->belongsTo(VirtualCurrency::class, 'virtual_currency_exchange_id');
    }
}
