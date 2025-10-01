<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CustomEventTag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'year',
        'tag_name'
    ];
    protected $dates = [
        'created_at',
        'updated_at'
    ];
}
