<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanCraftScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'connection_quality',
        'main_fuse_value',
        'wire_length',
        'fuse_value',
        'product_mounting',
        'overall_wiring',
        'overall_workmanship_safety_factor',
        'protection_quality',
        'main_fuse_connection_quality',
        'wire_penetration',
        'mounting_quality',
        'fuse_block',
        'all_main_equipment_connection_quality',
        'overall_workmanship_quality',
        'battery_housing',
        'mounting_quality_of_front_fuse',
        'additional_ground_wire',
        'detail_workmanship',
        'overall_design_and_ideas',
        'impact_to_audience',
        'total',
        'deduction_point',
        'deduction_comment',
        'time_start',
        'time_end'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'connection_quality' => 'float',
        'main_fuse_value' => 'float',
        'wire_length' => 'float',
        'fuse_value' => 'float',
        'product_mounting' => 'float',
        'overall_wiring' => 'float',
        'overall_workmanship_safety_factor' => 'float',
        'protection_quality' => 'float',
        'main_fuse_connection_quality' => 'float',
        'wire_penetration' => 'float',
        'mounting_quality' => 'float',
        'fuse_block' => 'float',
        'all_main_equipment_connection_quality' => 'float',
        'overall_workmanship_quality' => 'float',
        'battery_housing' => 'float',
        'mounting_quality_of_front_fuse' => 'float',
        'additional_ground_wire' => 'float',
        'detail_workmanship' => 'float',
        'overall_design_and_ideas' => 'float',
        'impact_to_audience' => 'float',
        'total' => 'float',
        'deduction_point' => 'float',
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
