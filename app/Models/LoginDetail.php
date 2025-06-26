<?php

namespace App\Models;

use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginDetail extends Model
{
    use HasFactory, HasStatusFilter;
    protected $fillable = ['user_id', 'ip_address', 'country', 'country_code', 'region_name', 'city', 'zip', 'lat', 'lon', 'timezone', 'isp', 'login_at', 'status'];
}