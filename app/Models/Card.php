<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use App\Helpers\ModelHelperConfig;
use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    use HasFactory, ModelHelper, HasFilter;

    protected $guarded = [];
    protected $casts = [
        'categories' => 'json',
    ];
    const CARD_VARIANTS = ['basic', 'pro', 'gold', 'silver', 'premium'];
    public function modelHelperConfig(): ModelHelperConfig
    {
        return ModelHelperConfig::create()
            ->files(['preview'])
            ->generateUuid();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function card_orders(): HasMany
    {
        return $this->hasMany(CardOrder::class);
    }
    public function credit_cards(): HasMany
    {
        return $this->hasMany(CreditCard::class);
    }
}
