<?php

namespace App\Models;

use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory, HasStatusFilter;
    protected $fillable = ['subject', 'body', 'type', 'status'];
}
