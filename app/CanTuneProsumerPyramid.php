<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanTuneProsumerPyramid extends Model
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
        'total',
        'deduction_point',
        'deduction_comment',
        'can_tune_bracket_id',
        'time_start',
        'time_end',
        'status_assessment',
        'status_submit_professional'
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
        'total' => 'float',
        'deduction_point' => 'float',
        'status_assessment' => 'integer',
        'status_submit_professional' => 'integer'
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

    public function getStatusAssessmentAllProsumer($event_id) 
    {
        $countNotAssessed = CanTuneProsumerPyramid::select(
            'can_tune_prosumer_pyramids.event_member_class_id AS event_member_class_id',
            'users.id AS member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'tonal_low',
            'tonal_mid_bass',
            'tonal_mid_low',
            'tonal_mid_high',
            'tonal_high',
            'tonal_total',
            'deduction_point',
            'deduction_comment',
            'total',
            'can_tune_prosumer_pyramids.can_tune_bracket_id AS can_tune_bracket_id',
            'can_tune_brackets.name AS can_tune_bracket_name',
            'time_start',
            'time_end',
            'status_assessment'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_prosumer_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 2)
            ->where('event_members.event_id', '=', $event_id)
            ->where('can_tune_prosumer_pyramids.status_assessment', '=', 0)
            ->count();

        if ($countNotAssessed > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getStatusSubmitAllProsumer($event_id) 
    {
        $countNotAssessed = CanTuneProsumerPyramid::select(
            'can_tune_prosumer_pyramids.event_member_class_id AS event_member_class_id',
            'users.id AS member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'tonal_low',
            'tonal_mid_bass',
            'tonal_mid_low',
            'tonal_mid_high',
            'tonal_high',
            'tonal_total',
            'deduction_point',
            'deduction_comment',
            'total',
            'can_tune_prosumer_pyramids.can_tune_bracket_id AS can_tune_bracket_id',
            'can_tune_brackets.name AS can_tune_bracket_name',
            'time_start',
            'time_end',
            'status_assessment',
            'status_submit_professional'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_prosumer_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 2)
            ->where('event_members.event_id', '=', $event_id)
            ->where('can_tune_prosumer_pyramids.status_submit_professional', '=', 0)
            ->count();

        if ($countNotAssessed > 0) {
            return false;
        } else {
            return true;
        }
    }
}
