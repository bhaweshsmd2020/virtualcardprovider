<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportLog extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'comment',
        'is_admin',
        'seen',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
