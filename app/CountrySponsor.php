<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountrySponsor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'country_id'
    ];

    protected $casts = [
        'user_id' => 'bigInteger',
        'country_id' => 'bigInteger',
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
