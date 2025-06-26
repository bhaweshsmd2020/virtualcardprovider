<?php

namespace App\Models;

use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CardOrder extends Model
{
    use HasFactory, HasFilter;

    protected $guarded = [];
    protected $casts = [
        'meta' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = CardOrder::max('id') + 1;
            $model->invoice_no = str_pad($model->id, 7, '0', STR_PAD_LEFT);
        });
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }

    public function credit_card(): HasOne
    {
        return $this->hasOne(CreditCard::class);
    }
    public function paymentLog(): HasOne
    {
        return $this->hasOne(PaymentLog::class);
    }
}
