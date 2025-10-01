<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'image',
        'status_delete',
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
