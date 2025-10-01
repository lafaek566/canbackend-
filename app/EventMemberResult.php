<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventMemberResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provocative',
        'fairness',
        'cooperation',
        'rules_competency',
        'scale',
        'score',
        'event_judge_member_assignment_id'
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'provocative' => 'integer',
        'fairness' => 'integer',
        'cooperation' => 'integer',
        'rules_competency' => 'integer',
        'scale' => 'integer',
        'score' => 'integer',
        'event_judge_member_assignment_id' => 'bigInteger'
    ];

    public function eventJudgeMemberAssignment()
    {
        return $this->belongsTo(EventJudgeMemberAssignment::class, 'event_judge_member_assignment_id');
    }

    public function getScaleByScore($score)
    {
        $intScore = floor($score);

        if ($intScore <= 0) {
            $scale = 0;
        } else if ($intScore <= 2 && $intScore >= 1) {
            $scale = 1;
        } else if ($intScore <= 6 && $intScore >= 3) {
            $scale = 2;
        } else if ($intScore <= 10 && $intScore >= 7) {
            $scale = 3;
        } else if ($intScore <= 13 && $intScore >= 11) {
            $scale = 4;
        } else if ($intScore <= 15 && $intScore >= 14) {
            $scale = 5;
        }

        return $scale;
    }

    public function getScoreAndScaleOfMemberByEvent($member_id, $event_id)
    {
        $eventMemberResult = EventMemberResult::select(
            'score'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_member_results.event_judge_member_assignment_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_members.event_id', '=', $event_id)
            ->get();

        $arr = [];

        foreach ($eventMemberResult as $object) {
            $arr[] = $object->toArray();
        }

        if (sizeof($arr) > 0) {
            $totalScore = 0;
            $averageScore = 0;
            $factor = sizeof($arr);
            $scale = 0;
            $intScore = 0;

            foreach ($arr as $score) {
                $score = (int) $score['score'];
                $totalScore = $totalScore + $score;
            }

            if ($totalScore <= 0) {
                $averageScore = 0;
            } else {
                $averageScore = $totalScore / $factor;
                $intScore = floor($averageScore);
            }

            if ($intScore <= 0) {
                $scale = 0;
            } else if ($intScore <= 2 && $intScore >= 1) {
                $scale = 1;
            } else if ($intScore <= 6 && $intScore >= 3) {
                $scale = 2;
            } else if ($intScore <= 10 && $intScore >= 7) {
                $scale = 3;
            } else if ($intScore <= 13 && $intScore >= 11) {
                $scale = 4;
            } else if ($intScore <= 15 && $intScore >= 14) {
                $scale = 5;
            }

            return array('score' => $averageScore, 'scale' => $scale);
        } else {
            return array('score' => null, 'scale' => null);
        }
    }

    public function getScoreAndScaleOfMember($member_id)
    {
        $eventMemberResult = EventMemberResult::select(
            'score'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_member_results.event_judge_member_assignment_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_members.member_id', '=', $member_id)
            ->get();

        $arr = [];

        foreach ($eventMemberResult as $object) {
            $arr[] = $object->toArray();
        }

        if (sizeof($arr) > 0) {
            $totalScore = 0;
            $averageScore = 0;
            $factor = sizeof($arr);
            $scale = 0;
            $intScore = 0;

            foreach ($arr as $score) {
                $score = (int) $score['score'];
                $totalScore = $totalScore + $score;
            }

            if ($totalScore <= 0) {
                $averageScore = 0;
            } else {
                $averageScore = $totalScore / $factor;
                $intScore = floor($averageScore);
            }

            if ($intScore <= 0) {
                $scale = 0;
            } else if ($intScore <= 2 && $intScore >= 1) {
                $scale = 1;
            } else if ($intScore <= 6 && $intScore >= 3) {
                $scale = 2;
            } else if ($intScore <= 10 && $intScore >= 7) {
                $scale = 3;
            } else if ($intScore <= 13 && $intScore >= 11) {
                $scale = 4;
            } else if ($intScore <= 15 && $intScore >= 14) {
                $scale = 5;
            }

            return array('score' => $averageScore, 'scale' => $scale);
        } else {
            return array('score' => null, 'scale' => null);
        }
    }
}
