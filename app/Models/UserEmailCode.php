<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Exception;
use Mail;

class UserEmailCode extends Model
{
    use HasFactory;
    public $table = "user_email_codes";
  
    protected $fillable = [
        'user_id',
        'code',
    ];
}
