<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\EventJudge;

class EventJudgeResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attitude',
        'judging',
        'scoring',
        'cooperation_and_explanation',
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
        'attitude' => 'integer',
        'judging' => 'integer',
        'scoring' => 'integer',
        'cooperation_and_explanation' => 'integer',
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

    public function getScoreAndScaleOfJudgeByEvent($judge_id, $event_id)
    {
        $eventJudgeResult = EventJudgeResult::select(
            'score'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_judge_results.event_judge_member_assignment_id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->where('event_judges.judge_id', '=', $judge_id)
            ->where('event_judges.event_id', '=', $event_id)
            ->get();

        $arr = [];

        foreach ($eventJudgeResult as $object) {
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

    public function getScoreAndScaleOfJudge($judge_id)
    {
        $eventJudgeResult = EventJudgeResult::select(
            'score'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_judge_results.event_judge_member_assignment_id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->where('event_judges.judge_id', '=', $judge_id)
            ->get();

        $arr = [];

        foreach ($eventJudgeResult as $object) {
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
