<?php

namespace App\Models;

use App\Traits\HasFilter;
use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CardlimitRequest extends Model
{
    use HasFactory, HasStatusFilter, HasFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_id',
        'fees',
        'subtotal',
        'total',
        'status',
    ];

    public function credit_card(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class, 'card_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }    
}
