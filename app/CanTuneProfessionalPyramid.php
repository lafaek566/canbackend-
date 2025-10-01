<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanTuneProfessionalPyramid extends Model
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
        'tonal_total',
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
        'staging_total',
        'deduction_point',
        'deduction_comment',
        'grand_total',
        'can_tune_bracket_id',
        'time_start',
        'time_end',
        'status_assessment',
        'status_submit_final'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'can_tune_bracket_id' => 'bigInteger',
        'tonal_low' => 'float',
        'tonal_mid_bass' => 'float',
        'tonal_mid_low' => 'float',
        'tonal_mid_high' => 'float',
        'tonal_high' => 'float',
        'tonal_total' => 'float',
        'staging_left' => 'float',
        'staging_right' => 'float',
        'height_left' => 'float',
        'height_lfctr' => 'float',
        'height_center' => 'float',
        'height_rhctr' => 'float',
        'height_right' => 'float',
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
        'staging_total' => 'float',
        'deduction_point' => 'float',
        'grand_total' => 'float',
        'status_assessment' => 'integer',
        'status_submit_final' => 'integer'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function eventMemberClass()
    {
        return $this->belongsTo(EventMemberClass::class, 'event_member_class_id');
    }

    public function getStatusAssessmentAllProfessional($event_id) 
    {
        $countNotAssessed = CanTuneProfessionalPyramid::select(
            'can_tune_professional_pyramids.event_member_class_id AS event_member_class_id',
            'status_assessment'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_professional_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_professional_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 3)
            ->where('event_members.event_id', '=', $event_id)
            ->where('can_tune_professional_pyramids.status_assessment', '=', 0)
            ->count();

        if ($countNotAssessed > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getStatusSubmitAllProfessional($event_id) 
    {
        $countNotAssessed = CanTuneProsumerPyramid::select(
            'can_tune_professional_pyramids.event_member_class_id AS event_member_class_id',
            'status_assessment',
            'status_submit_professional'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_professional_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_professional_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 3)
            ->where('event_members.event_id', '=', $event_id)
            ->where('can_tune_professional_pyramids.status_submit_professional', '=', 0)
            ->count();

        if ($countNotAssessed > 0) {
            return false;
        } else {
            return true;
        }
    }
}
