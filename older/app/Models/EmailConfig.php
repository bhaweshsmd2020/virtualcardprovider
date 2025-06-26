<?php

namespace App\Models;

use App\Traits\HasStatusFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    use HasFactory, HasStatusFilter;
    protected $fillable = ['email_protocol', 'email_encryption', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'from_address', 'from_name', 'status', 'notification_email'];
}
