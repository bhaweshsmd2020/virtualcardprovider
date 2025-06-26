<?php

namespace App\Models;

use App\Traits\HasFilter;
use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardLimit extends Model
{
    use HasFactory, HasStatusFilter, HasFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_type',
        'limit',
        'topup_limit',
        'spending_limit',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
