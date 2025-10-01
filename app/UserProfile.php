<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'avatar',
        'banner', 
        'biography',
        'phone_no',
        'phone_verified_at',
        'phone_request_id',
        'user_id', // Ini wajib agar bisa diisi saat UserProfile::create
    ];

    /**
     * The dates attributes.
    *
    * @var array
    */
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'user_id' => 'bigInteger',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
