<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventJudgeActivity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_judge_id',
        'competition_activity_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_judge_id' => 'bigInteger',
        'competition_activity_id' => 'bigInteger'
    ];

    public function eventJudge()
    {
        return $this->belongsTo(EventJudge::class, 'event_judge_id');
    }
    public function competitionActivity()
    {
        return $this->belongsTo(CompetitionActivity::class, 'competition_activity_id');
    }

    public function getEventJudgeActivityAndStatus($event_judge_id)
    {
        $arrEventJudgeActivity = $this->getEventJudgeActivitiesByEventJudgeId($event_judge_id);

        for ($i = 0; $i < sizeof($arrEventJudgeActivity); $i++) {
            $status = $this->getEventJudgeMemberAssignmentStatusByEventJudgeActivityId($arrEventJudgeActivity[$i]['event_judge_activity_id']);
            $hasJudged = $this->getEventJudgeMemberHasJudged($arrEventJudgeActivity[$i]['event_judge_activity_id']);
            $arrEventJudgeActivity[$i]['status'] = $status;
            $arrEventJudgeActivity[$i]['hasJudged'] = $hasJudged;
        }

        return $arrEventJudgeActivity;
    }

    public function getEventJudgeActivitiesByEventJudgeId($event_judge_id)
    {
        $eventJudgeActivity = EventJudgeActivity::select(
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_activities.event_judge_id AS event_judge_id',
            'event_judge_activities.competition_activity_id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name'
        )
            ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
            ->where('event_judge_activities.event_judge_id', '=', $event_judge_id)
            ->get();

        $arr = [];

        foreach ($eventJudgeActivity as $object) {
            $arr[] = $object->toArray();
        }

        return $arr;
    }

    public function getEventJudgeMemberAssignmentStatusByEventJudgeActivityId($event_judge_activity_id)
    {
        $count = EventJudgeMemberAssignment::select(
            'event_judge_member_assignments.id AS event_judge_member_assignment_id'
        )
            ->where('event_judge_member_assignments.event_judge_activity_id', '=', $event_judge_activity_id)
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getEventJudgeMemberHasJudged($event_judge_activity_id)
    {
        $eventMemberClasses = EventJudgeMemberAssignment::select(
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_judge_member_assignments.event_member_class_id AS event_member_class_id',
            'event_member_classes.status_score AS status_score'
        )
            ->leftJoin('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->where('event_judge_member_assignments.event_judge_activity_id', '=', $event_judge_activity_id)
            ->get();


        foreach ($eventMemberClasses as $eventMemberClass) {
            if ($eventMemberClass->status_score == 1) {
                return true;
            }
        }

        return false;

        // if ($count > 0) {
        //     return true;
        // } else {
        //     return false;
        // }
    }
}
