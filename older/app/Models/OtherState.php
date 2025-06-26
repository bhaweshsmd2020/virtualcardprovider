<?php

namespace App\Models;

use App\Traits\HasFilter;
use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherState extends Model
{
    use HasFactory, HasStatusFilter, HasFilter;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'state_id',
        'name',
        'status',
    ];
}
