<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTagGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'tag_id'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
