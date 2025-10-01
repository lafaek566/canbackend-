<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanLoudScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'first_round',
        'second_round',
        'deduction_battery',
        'deduction_point',
        'deduction_comment',
        'total',
        'status_assessment',
        'time_start',
        'time_end'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'first_round' => 'float',
        'second_round' => 'float',
        'deduction_battery' => 'float',
        'deduction_point' => 'float',
        'total' => 'float',
        'status_assessment' => 'integer'
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
