<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Gallery;
use Exception;
use Mail;
use App\Mail\SendEmailCode;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'password',
        'device_token'
    ];


    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }

    public function toArray()
    {
        $data = parent::toArray();

        // Include gallery data
        $data['gallery'] = $this->gallery;

        return $data;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** 
     * wrute code on method
     * 
     * @return response()
     */
    public function generateCode()
    {
        $code = rand(100000, 999999);
  
        UserEmailCode::updateOrCreate(
            [ 'user_id' => auth()->user()->id ],
            [ 'code' => $code ]
        );
    
        try {
  
            $details = [
                'name' => auth()->user()->name,
                'code' => $code
            ];
             
            Mail::to(auth()->user()->email)->send(new SendEmailCode($details));
    
        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
    }
     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }    
}
