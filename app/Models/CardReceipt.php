<?php

namespace App\Models;

use App\Traits\HasFilter;
use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardReceipt extends Model
{
    use HasFactory, HasStatusFilter, HasFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'phone',
        'image',
        'comments',
        'status',
    ];
}
