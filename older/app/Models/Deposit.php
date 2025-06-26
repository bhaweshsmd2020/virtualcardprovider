<?php

namespace App\Models;

use App\Helpers\ModelHelperConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\ModelHelper;
use App\Traits\HasFilter;

class Deposit extends Model
{
    use HasFactory, ModelHelper, HasFilter;
    protected $casts = [
        'meta' => 'json'
    ];
    protected $guarded = [];

    public function modelHelperConfig(): ModelHelperConfig
    {
        return ModelHelperConfig::create()->generateInvoice();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function credit_card(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }
    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }
}
