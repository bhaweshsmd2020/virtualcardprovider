<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use App\Helpers\ModelHelperConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualCurrency extends Model
{
    use HasFactory, ModelHelper;

    protected $guarded = [];

    public function modelHelperConfig(): ModelHelperConfig
    {
        return ModelHelperConfig::create()
            ->files(['preview']);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class);
    }
}
