<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventJudgeResult;
use Illuminate\Support\Facades\Validator;
use App\EventJudge;
use App\EventMember;
use App\User;

class EventJudgeResultController extends Controller
{
    public $successStatus = 200;

    public function ratingsFromParticipant(Request $request)
    {
        $eventJudgeResult = EventJudgeResult::select(
            'event_judge_results.id AS id',
            'attitude',
            'judging',
            'scoring',
            'cooperation_and_explanation',
            'scale',
            'score',
            'event_members.id AS event_member_id',
            'event_members.member_id AS member_id',
            'event_judges.id AS event_judge_id',
            'event_judges.judge_id AS judge_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'event_member_classes.id AS event_member_class_id',
            'class_groups.name AS class_group_name',
            'class_grades.name AS class_grade_name',
            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',
            'competitions.id AS competition_id',
            'competitions.title AS competition_title',
            'events.title AS event_title',
            'events.id AS event_id'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_judge_results.event_judge_member_assignment_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->join('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
            ->join('competitions', 'competitions.id', '=', 'competition_activities.competition_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventJudgeResultCount = EventJudgeResult::select(
            'event_judge_results.id AS id',
            'attitude',
            'judging',
            'scoring',
            'cooperation_and_explanation',
            'scale',
            'score',
            'event_members.id AS event_member_id',
            'event_members.member_id AS member_id',
            'event_judges.id AS event_judge_id',
            'event_judges.judge_id AS judge_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'event_member_classes.id AS event_member_class_id',
            'class_groups.name AS class_group_name',
            'class_grades.name AS class_grade_name',
            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',
            'competitions.id AS competition_id',
            'competitions.title AS competition_title',
            'events.title AS event_title',
            'events.id AS event_id'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_judge_results.event_judge_member_assignment_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->join('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
            ->join('competitions', 'competitions.id', '=', 'competition_activities.competition_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->count();

        $arr = [];

        foreach ($eventJudgeResult as $object) {
            $arr[] = $object->toArray();
        }


        return response()->json(['data' => $arr, 'total' => $eventJudgeResultCount]);
    }

    public function listEventJudgesRated(Request $request) 
    {
        
        $eventJudgesToRate = EventMember::select(
            'event_judge_results.id AS event_judge_results_id',
            'event_judge_results.attitude',
            'event_judge_results.judging',
            'event_judge_results.scoring',
            'event_judge_results.cooperation_and_explanation',
            'event_judge_results.scale',
            'event_judge_results.score',

            'event_judge_activities.competition_activity_id AS event_judge_activity_competition_activity_id',
            'event_judge_activities.event_judge_id AS event_judge_activity_event_judge_id',
            'event_judges.judge_id AS event_judges_id',
            'users.name AS judge_name',
            'user_profiles.avatar AS avatar',
            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',
            'class_grades.name AS class_grade_name',
            'class_groups.name AS class_group_name',
            // 'event_judges_results.score AS judge_score',
            // 'event_judges_results.scale AS judge_scale',
            'event_members.id AS event_member_id',
            'event_member_classes.id AS event_member_class_id',
            // 'event_member_classes.form_assessment AS form_assessment',
            // 'event_member_classes.judge_signature AS judge_signature',
            // 'event_member_classes.participant_signature AS participant_signature',
            'event_member_classes.grand_total AS grand_total',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id'
        )
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
            ->join('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->join('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('event_judge_results', 'event_judge_results.event_judge_member_assignment_id', '=', 'event_judge_member_assignments.id')
            ->where('event_members.member_id', $request->member_id)
            ->where('event_members.event_id', $request->event_id)
            // ->where('event_judge_results.score', '<>', null)      
            // ->where('event_judge_results.scale', '<>', null)         
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();


        $eventJudgesToRateCount = EventMember::select(
            'event_judge_results.id AS event_judge_results_id',
            'event_judge_results.attitude',
            'event_judge_results.judging',
            'event_judge_results.scoring',
            'event_judge_results.cooperation_and_explanation',
            'event_judge_results.scale',
            'event_judge_results.score',

            'event_judge_activities.competition_activity_id AS event_judge_activity_competition_activity_id',
            'event_judge_activities.event_judge_id AS event_judge_activity_event_judge_id',
            'event_judges.judge_id AS event_judges_id',
            'users.name AS judge_name',
            'user_profiles.avatar AS avatar',
            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',
            'class_grades.name AS class_grade_name',
            'class_groups.name AS class_group_name',
            // 'event_judges_results.score AS judge_score',
            // 'event_judges_results.scale AS judge_scale',
            'event_members.id AS event_member_id',
            'event_member_classes.id AS event_member_class_id',
            // 'event_member_classes.form_assessment AS form_assessment',
            // 'event_member_classes.judge_signature AS judge_signature',
            // 'event_member_classes.participant_signature AS participant_signature',
            'event_member_classes.grand_total AS grand_total',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id'
        )
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
            ->join('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->join('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('event_judge_results', 'event_judge_results.event_judge_member_assignment_id', '=', 'event_judge_member_assignments.id')
            ->where('event_members.member_id', $request->member_id)
            ->where('event_members.event_id', $request->event_id)
            // ->where('event_judge_results.score', '<>', null)      
            // ->where('event_judge_results.scale', '<>', null)            
            ->count();


        $arr = [];

        foreach ($eventJudgesToRate as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgesToRateCount]);
    }

    public function listTopThreeJudges()
    {
        $user = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'name',
            'judge_rating'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->where('role_id', '=', 5)
            ->where('judge_rating', '<>', null)
            ->orderBy('judge_rating', 'desc')
            ->orderBy('name', 'asc')
            ->limit(3)
            ->get();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventJudgeResult = new EventJudgeResult();
            $scale = $eventJudgeResult->getScaleByScore($arr[$i]['judge_rating']);
            $arr[$i]['scale'] = $scale;
        }


        return response()->json(['data' => $arr]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attitude' => 'required',
            'judging' => 'required',
            'scoring' => 'required',
            'cooperation_and_explanation' => 'required',
            'event_judge_member_assignment_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $eventJudgeCount = EventJudgeResult::select(
            'id',
            'event_judge_member_assignment_id'
        )
            ->where('event_judge_member_assignment_id', '=', $request->event_judge_member_assignment_id)
            ->get()->count();

        if ($eventJudgeCount > 0) {
            return response()->json(['status' => 'failed', 'message' => 'cannot rate judge because this judge have been rated in this class and event'], 200);
        }

        $attitude = (int) $request->attitude;
        $judging = (int) $request->judging;
        $scoring = (int) $request->scoring;
        $cooperation_and_explanation = (int) $request->cooperation_and_explanation;
        $score = $attitude + $judging + $scoring + $cooperation_and_explanation;
        $scale = 0;

        if ($score >= 14 && $score <= 15) {
            $scale = 5;
        } else if ($score >= 11 && $score <= 13) {
            $scale = 4;
        } else if ($score >= 7 && $score <= 10) {
            $scale = 3;
        } else if ($score >= 3 && $score <= 6) {
            $scale = 2;
        } else if ($score >= 1 && $score <= 2) {
            $scale = 1;
        } else {
            $scale = 0;
        }

        $input['attitude'] = $attitude;
        $input['judging'] = $judging;
        $input['scoring'] = $scoring;
        $input['cooperation_and_explanation'] = $cooperation_and_explanation;
        $input['score'] = $score;
        $input['scale'] = $scale;
        $input['event_judge_member_assignment_id'] = $request->event_judge_member_assignment_id;

        $save = EventJudgeResult::create($input);

        if ($save) {
            $this->countJudgeRating($request->event_judge_member_assignment_id);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }

        if ($save) {
            return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    public function countJudgeRating($event_judge_member_assignment_id)
    {
        $eventJudge = EventJudge::join('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->where('event_judge_member_assignments.id', '=', $event_judge_member_assignment_id)
            ->first();

        $judge_id = $eventJudge->judge_id;

        $eventJudgeResult = new EventJudgeResult();
        $result = $eventJudgeResult->getScoreAndScaleOfJudge($judge_id);


        $update = User::where('id', $judge_id)->update(
            [
                'judge_rating' => $result['score']
            ]
        );
    }
}
