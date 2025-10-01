<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rules extends Model
{
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'title',
        'description',
        'link',
        'alt'
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
}
