<?php

namespace App\Models;

use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory, HasStatusFilter;

    protected $fillable = [
        'country_id',
        'name',
        'status',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
