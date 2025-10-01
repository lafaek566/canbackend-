<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DanceSubAssessment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'dance_major_aspect_id'
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'dance_major_aspect_id' => 'bigInteger'
    ];

    public function danceMajorAspect()
    {
        return $this->belongsTo(DanceMajorAspect::class,'dance_major_aspect_id');
    }
}
