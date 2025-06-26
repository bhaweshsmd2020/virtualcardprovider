<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use App\Helpers\ModelHelperConfig;
use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditCard extends Model
{
    use HasFactory, ModelHelper, HasFilter;

    public function modelHelperConfig(): ModelHelperConfig
    {
        return ModelHelperConfig::create()
            ->generateUuid();
    }
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
    public function virtual_card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id');
    }
    public function card_order(): BelongsTo
    {
        return $this->belongsTo(CardOrder::class);
    }
    public function cardHolder(): BelongsTo
    {
        return $this->belongsTo(CardHolder::class);
    }
}
