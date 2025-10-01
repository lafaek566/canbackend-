<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanQScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'vision_block',
        'seating_position',
        'noise_floor',
        'alternator_whine',
        'coming_late',
        'system_down',
        'system_volume_level_suggested_one',
        'system_volume_level_suggested_two',
        'system_volume_level_suggested_three',
        'system_volume_level_suggested_use',
        'cheating_action',
        'cheating_comment',
        'deduction_point',
        'deduction_comment',
        'grand_total',
        'time_start',
        'time_end',
        'status_assessment'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'vision_block' => 'float',
        'seating_position' => 'float',
        'noise_floor' => 'float',
        'alternator_whine' => 'float',
        'coming_late' => 'float',
        'system_down' => 'float',
        'system_volume_level_suggested_one' => 'float',
        'system_volume_level_suggested_two' => 'float',
        'system_volume_level_suggested_three' => 'float',
        'system_volume_level_suggested_use' => 'float',
        'cheating_action' => 'float',
        'deduction_point' => 'float',
        'grand_total' => 'float',
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
