<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'email_confirm_code',
        'password',
        // 'api_token', // DIHAPUS: Menggunakan Passport
        'role_id',
        'sponsor_type',
        'sponsor_tier',
        'country_id',
        'association_id',
        'judge_rating',
        'member_rating',
        'can_q_consumer_point',
        'can_q_prosumer_point',
        'can_q_professional_point',
        'status_banned',
        'manual_input',
        'grouped_user_id'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role_id' => 'integer',
        'sponsor_type' => 'integer',
        'judge_rating' => 'float',
        'member_rating' => 'float',
        'can_q_consumer_point' => 'integer',
        'can_q_prosumer_point' => 'integer',
        'can_q_professional_point' => 'integer',
        'status_banned' => 'integer',
        'included_in_leaderboard' => 'integer',
    ];

    // public function generateToken() // DIHAPUS karena menggunakan Passport
    // {
    //     $this->api_token = str_random(60);
    //     $this->save();
    //     return $this->api_token;
    // }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function userCars()
    {
        return $this->hasMany(Car::class, 'user_id')->orderBy('index');
    }
}