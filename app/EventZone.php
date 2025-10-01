<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventZone extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'zone_name',
        'country_id'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
