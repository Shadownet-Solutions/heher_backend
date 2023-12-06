<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Exception;
use Mail;

class ResetPassword extends Model
{
    use HasFactory;
    public $table = "reset_passwords";

    protected $fillable = [
        'user_id',
        'email',
        'code'
    ];
}
