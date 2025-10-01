<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventActivityForm extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_competition_activity_id',
        'event_form_generator_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_competition_activity_id' => 'bigInteger',
        'event_form_generator_id' => 'bigInteger'
    ];
}
