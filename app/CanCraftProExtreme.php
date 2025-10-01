<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanCraftProExtreme extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'items',
        'comment_participant',
        'comment_judge',
        'point'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'point' => 'float'
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

    public function eventMemberClass()
    {
        return $this->belongsTo(EventMemberClass::class, 'event_member_class_id');
    }
}
