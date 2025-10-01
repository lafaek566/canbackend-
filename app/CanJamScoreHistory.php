<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanJamScoreHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'db_score',
        'system_down',
        'deduction_point',
        'deduction_comment',
        'total',
        'time_start',
        'time_end'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'db_score' => 'float',
        'system_down' => 'float',
        'deduction_point' => 'float',
        'total' => 'float'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'time_start',
        'time_end'
    ];

    public function eventMemberClass()
    {
        return $this->belongsTo(EventMemberClass::class, 'event_member_class_id');
    }
}
