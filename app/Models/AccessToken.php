<?php

namespace App\Models;

use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory, HasStatusFilter;
    
    protected $fillable = ['access_token', 'token_type', 'expires_in', 'refresh_token', 'scope', 'status'];
}
