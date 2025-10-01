<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanPerformScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'tonal_low',
        'tonal_mid_bass',
        'tonal_mid_low',
        'tonal_mid_high',
        'tonal_high',
        'spectral_balance',
        'linearity',
        'noise_floor',
        'alternator_whine',
        'coming_late',
        'system_down',
        'tonal_total',
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
        'grand_total',
        'deduction_point',
        'deduction_comment',
        'time_start',
        'time_end'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'tonal_low' => 'float',
        'tonal_mid_bass' => 'float',
        'tonal_mid_low' => 'float',
        'tonal_mid_high' => 'float',
        'tonal_high' => 'float',
        'listening_low' => 'float',
        'listening_mid_bass' => 'float',
        'listening_mid_low' => 'float',
        'listening_mid_high' => 'float',
        'listening_high' => 'float',
        'spectral_balance' => 'float',
        'linearity' => 'float',
        'noise_floor' => 'float',
        'alternator_whine' => 'float',
        'coming_late' => 'float',
        'system_down' => 'float',
        'tonal_total' => 'float',
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
