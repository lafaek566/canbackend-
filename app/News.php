<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'thumbnail',
        'user_id',
        'updated_by_user_id',
        'country_id',
        'association_id',
        'date'
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'bigInteger',
        'country_id' => 'bigInteger',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class,'updated_by_user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id');
    }
}
