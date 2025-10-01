<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanQImagingPositionAndFocus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'left_drum',
        'left_guitar',
        'left_piano',
        'left_vibraphone',
        'left_trumpet',
        'left_total',
        'lfctr_drum',
        'lfctr_guitar',
        'lfctr_piano',
        'lfctr_vibraphone',
        'lfctr_trumpet',
        'lfctr_total',
        'rhctr_drum',
        'rhctr_guitar',
        'rhctr_piano',
        'rhctr_vibraphone',
        'rhctr_trumpet',
        'rhctr_total',
        'right_drum',
        'right_guitar',
        'right_piano',
        'right_vibraphone',
        'right_trumpet',
        'right_total',
        'total_imaging_position_and_focus'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'left_drum' => 'float',
        'left_guitar' => 'float',
        'left_piano' => 'float',
        'left_vibraphone' => 'float',
        'left_trumpet' => 'float',
        'left_total' => 'float',
        'lfctr_drum' => 'float',
        'lfctr_guitar' => 'float',
        'lfctr_piano' => 'float',
        'lfctr_vibraphone' => 'float',
        'lfctr_trumpet' => 'float',
        'lfctr_total' => 'float',
        'center_drum' => 'float',
        'center_guitar' => 'float',
        'center_piano' => 'float',
        'center_vibraphone' => 'float',
        'center_trumpet' => 'float',
        'center_total' => 'float',
        'rhctr_drum' => 'float',
        'rhctr_guitar' => 'float',
        'rhctr_piano' => 'float',
        'rhctr_vibraphone' => 'float',
        'rhctr_trumpet' => 'float',
        'rhctr_total' => 'float',
        'right_drum' => 'float',
        'right_guitar' => 'float',
        'right_piano' => 'float',
        'right_vibraphone' => 'float',
        'right_trumpet' => 'float',
        'right_total' => 'float',
        'total_imaging_position_and_focus' => 'float'
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
