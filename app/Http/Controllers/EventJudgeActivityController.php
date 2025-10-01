<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventJudgeActivity;

class EventJudgeActivityController extends Controller
{
    public $successStatus = 200;

    public function getActivityAssignedToJudge(Request $request)
    {
        $eventJudgeActivity = EventJudgeActivity::select(
            'event_judge_activities.competition_activity_id',
            'competition_activities.name AS competition_activity_name'
        )
        ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
        ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
        ->where('event_judges.event_id', '=', $request->event_id)
        ->whereNotNull('event_judge_member_assignments.id')
        ->get();

        $eventJudgeActivityCount = $eventJudgeActivity->count();

        $arr = [];

        foreach ($eventJudgeActivity as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeActivityCount]);
    }
}
