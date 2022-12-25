<?php

namespace App\Models;

use App\Jobs\TestEmailJob;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Exception;
use Mail;
use App\Mail\SendCodeMail;
use Carbon\Carbon;
use ESolution\DBEncryption\Traits\EncryptedAttribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, EncryptedAttribute;


     /**
         * The attributes that should be encrypted on save.
         *
         * @var array
         */
        protected $encryptable = [
            'name',
            'phone',
            'dob',
            'email',
            'city'
        ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'dob',
        'city'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    //This method automatically save date Y-m-d format in database



     //This method automatically fetch date d-m-Y format from database

    // public function getDobAttribute($date)
    // {
    //    return !is_null($date) ? (Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y')) : null;
    // }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generateCode()
    {
        $code = rand(1000, 9999);

        UserCode::updateOrCreate(
            [ 'user_id' => auth()->user()->id ],
            [ 'code' => $code ]
        );

        try {

            $details = [
                'title' => 'Mail from Test',
                'code' => $code
            ];


            dispatch(new TestEmailJob($details, $this->email));


        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
        }
    }
}
