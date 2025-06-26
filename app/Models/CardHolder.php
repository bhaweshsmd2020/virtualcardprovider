<?php

namespace App\Models;

use App\Helpers\ModelHelper;
use App\Helpers\ModelHelperConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardHolder extends Model
{
    use HasFactory, ModelHelper;

    protected $guarded = [];

    public function modelHelperConfig(): ModelHelperConfig
    {
        return ModelHelperConfig::create()
            ->generateUuid();
    }
}
