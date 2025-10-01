<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanQTonalAccuracy extends Model
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
        'tonal_total'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'tonal_low' => 'float',
        'tonal_mid_bass' => 'float',
        'tonal_mid_low' => 'float',
        'tonal_mid_high' => 'float',
        'tonal_high' => 'float',
        'tonal_total' => 'float'
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
