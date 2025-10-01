<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventJudgeMemberAssignment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'event_judge_activity_id',
        'form_generator_id',
        'order'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'event_judge_activity_id' => 'bigInteger',
        'form_generator_id' => 'bigInteger'
    ];

    public function eventMemberClass()
    {
        return $this->belongsTo(EventMemberClass::class, 'event_member_class_id');
    }
    public function eventJudgeActivity()
    {
        return $this->belongsTo(EventJudgeActivity::class, 'event_judge_activity_id');
    }
    public function eventMemberJudgeForm()
    {
        return $this->belongsTo(FormGenerator::class, 'form_generator_id');
    }

    public function getStatusAssignment($event_judge_member_assignment_id)
    {
        if ($event_judge_member_assignment_id === null) {
            return false;
        } else {
            return true;
        }
    }

    public function getJudgeAssignToParticipant($event_member_class_id)
    {
        $eventJudgeMemberAssignment = EventJudgeMemberAssignment::select(
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_judge_member_assignments.event_member_class_id AS event_member_class_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_activity_id',
            'users.id AS  judge_id',
            'users.name AS judge_name',
            'user_profiles.avatar AS judge_avatar'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where('event_member_classes.id', '=', $event_member_class_id)
            ->get();

        $count = $eventJudgeMemberAssignment->count();

        $arr = [];

        if ($count === 0) {
            return $arr;
        } else {
            foreach ($eventJudgeMemberAssignment as $object) {
                $arr[] = $object->toArray();
            }

            return $arr;
        }
    }
}
