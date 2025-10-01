<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanQStaging extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'staging_left',
        'staging_right',
        'height_left',
        'height_lfctr',
        'height_center',
        'height_rhctr',
        'height_right',
        'height_total',
        'distance_left',
        'distance_lfctr',
        'distance_center',
        'distance_rhctr',
        'distance_right',
        'distance_total',
        'depth_c1_to_c2',
        'depth_c2_to_c3',
        'depth_total',
        'staging_total'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'staging_left' => 'float',
        'staging_right' => 'float',
        'height_left' => 'float',
        'height_lfctr' => 'float',
        'height_center' => 'float',
        'height_rhctr' => 'float',
        'height_total' => 'float',
        'distance_left' => 'float',
        'distance_lfctr' => 'float',
        'distance_center' => 'float',
        'distance_rhctr' => 'float',
        'distance_right' => 'float',
        'distance_total' => 'float',
        'depth_c1_to_c2' => 'float',
        'depth_c2_to_c3' => 'float',
        'depth_total' => 'float',
        'staging_total' => 'float'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function eventMemberClass()
    {
        return $this->belongsTo(EventMemberClass::class, 'event_member_class_id');
    }
}
