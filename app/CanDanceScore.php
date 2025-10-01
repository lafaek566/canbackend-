<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanDanceScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'theme',
        'choreography',
        'costume',
        'movement_quality',
        'balance',
        'dynamic_and_speed',
        'lower_body_activity',
        'upper_body_activity',
        'facial_expression',
        'team_confidence',
        'impact_to_audience',
        'dance_total',
        'deduction_point',
        'deduction_comment',
        'grand_total',
        'time_start',
        'time_end'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'theme' => 'float',
        'choreography' => 'float',
        'costume' => 'float',
        'movement_quality' => 'float',
        'balance' => 'float',
        'dynamic_and_speed' => 'float',
        'lower_body_activity' => 'float',
        'upper_body_activity' => 'float',
        'facial_expression' => 'float',
        'team_confidence' => 'float',
        'impact_to_audience' => 'float',
        'dance_total' => 'float',
        'grand_total' => 'float',
        'deduction_point' => 'float'
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
