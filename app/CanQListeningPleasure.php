<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanQListeningPleasure extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'listening_low_distorted',
        'listening_low_vibration',
        'listening_low_loudness',
        'listening_low_rear_bass',
        'listening_low_less_low_extention',
        'listening_low_boomy_blur_muddy',
        'listening_low_definition',
        'listening_low_total',
        'listening_mid_bass_distorted',
        'listening_mid_bass_vibration',
        'listening_mid_bass_loudness',
        'listening_mid_bass_position_unstable',
        'listening_mid_bass_lr_timbre_different',
        'listening_mid_bass_stiff_thin_dry',
        'listening_mid_bass_boomy_blur_muddy',
        'listening_mid_bass_definition',
        'listening_mid_bass_total',
        'listening_mid_low_distorted',
        'listening_mid_low_loudness',
        'listening_mid_low_position_unstable',
        'listening_mid_low_lr_timbre_different',
        'listening_mid_low_clinical_thin_dry',
        'listening_mid_low_boxy_blur_muddy',
        'listening_mid_low_definition',
        'listening_mid_low_total',
        'listening_mid_high_distorted',
        'listening_mid_high_loudness',
        'listening_mid_high_position_unstable',
        'listening_mid_high_lr_timbre_different',
        'listening_mid_high_clinical_dry',
        'listening_mid_high_blur_honkey',
        'listening_mid_high_harsh_sibilance',
        'listening_mid_high_total',
        'listening_high_distorted',
        'listening_high_loudness',
        'listening_high_lr_timbre_different',
        'listening_high_dry_clinical_metallic',
        'listening_high_blur_dull',
        'listening_high_harsh_sibilance',
        'listening_high_total',
        'listening_total'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'listening_low_distorted' => 'float',
        'listening_low_vibration' => 'float',
        'listening_low_loudness' => 'float',
        'listening_low_rear_bass' => 'float',
        'listening_low_less_low_extention' => 'float',
        'listening_low_boomy_blur_muddy' => 'float',
        'listening_low_definition' => 'float',
        'listening_low_total' => 'float',
        'listening_mid_bass_distorted' => 'float',
        'listening_mid_bass_vibration' => 'float',
        'listening_mid_bass_loudness' => 'float',
        'listening_mid_bass_position_unstable' => 'float',
        'listening_mid_bass_lr_timbre_different' => 'float',
        'listening_mid_bass_stiff_thin_dry' => 'float',
        'listening_mid_bass_boomy_blur_muddy' => 'float',
        'listening_mid_bass_definition' => 'float',
        'listening_mid_bass_total' => 'float',
        'listening_mid_low_distorted' => 'float',
        'listening_mid_low_loudness' => 'float',
        'listening_mid_low_position_unstable' => 'float',
        'listening_mid_low_lr_timbre_different' => 'float',
        'listening_mid_low_clinical_thin_dry' => 'float',
        'listening_mid_low_boxy_blur_muddy' => 'float',
        'listening_mid_low_definition' => 'float',
        'listening_mid_low_total' => 'float',
        'listening_mid_high_distorted' => 'float',
        'listening_mid_high_loudness' => 'float',
        'listening_mid_high_position_unstable' => 'float',
        'listening_mid_high_lr_timbre_different' => 'float',
        'listening_mid_high_clinical_dry' => 'float',
        'listening_mid_high_blur_honkey' => 'float',
        'listening_mid_high_harsh_sibilance' => 'float',
        'listening_mid_high_total' => 'float',
        'listening_high_distorted' => 'float',
        'listening_high_loudness' => 'float',
        'listening_high_lr_timbre_different' => 'float',
        'listening_high_dry_clinical_metallic' => 'float',
        'listening_high_blur_dull' => 'float',
        'listening_high_harsh_sibilance' => 'float',
        'listening_high_total' => 'float',
        'listening_total' => 'float'
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
