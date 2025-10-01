<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;

use App\EventJudge;
use App\EventJudgeResult;
use League\Event\Event;
use App\User;
use Illuminate\Support\Facades\DB;
use App\EventJudgeActivity;
use App\CompetitionActivity;
use App\Competition;
use App\EventMemberResult;
use App\EventMemberClass;
use App\EventJudgeMemberAssignment;
use App\EventMember;
use App\UserProfile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class EventJudgeController extends Controller
{
    public $successStatus = 200;

    public function listDetail(Request $request)
    {
        $eventJudge = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'events.title AS event_title',
            'users.name AS judge_name',
            'user_profiles.avatar AS avatar'
            // 'competitions.title AS judging'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->get();

        return response()->json(['data' => $eventJudge]);
    }

    public function listJudgesOfEventAndCompetitionLimit(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventJudge = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_id',
            'events.title AS event_title',
            'users.name AS judge_name',
            'user_profiles.avatar AS avatar',
            'competitions.title AS judging'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('competitions', 'competitions.id', '=', 'event_judges.competition_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.competition_id', '=', $request->competition_id)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $eventJudgeCount = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_id',
            'events.title AS event_title',
            'users.name AS judge_name',
            'user_profiles.avatar AS avatar',
            'competitions.title AS judging'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('competitions', 'competitions.id', '=', 'event_judges.competition_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.competition_id', '=', $request->competition_id)
            ->count();

        $arr = [];

        foreach ($eventJudge as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventJudgeResult = new EventJudgeResult();
            $result = $eventJudgeResult->getScoreAndScaleOfJudge($arr[$i]['judge_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeCount]);
    }

    public function listJudgesOfEventLimit(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventJudge = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'event_judges.created_at',
            'events.title AS event_title',
            'users.name AS judge_name',
            'users.manual_input AS judge_manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $eventJudgeCount = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'event_judges.created_at',
            'users.manual_input AS judge_manual_input',
            'users.name AS judge_name'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->count();


        $arr = [];

        foreach ($eventJudge as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventJudgeResult = new EventJudgeResult();
            $result = $eventJudgeResult->getScoreAndScaleOfJudge($arr[$i]['judge_id']);
            $eventJudgeActivity = new EventJudgeActivity();
            $competition_activities = $eventJudgeActivity->getEventJudgeActivityAndStatus($arr[$i]['id']);
            // $status = $eventJudge->getEventJudgeStatusAssign($arr[$i]['id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
            $arr[$i]['competition_activities'] = $competition_activities;
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeCount]);
    }

    public function listJudgesOfEventLimitOrder(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;


        $eventJudge = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'events.title AS event_title',
            'users.name AS judge_name',
            'users.manual_input AS manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->offset($offset)
            ->limit($limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventJudgeCount = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'users.manual_input AS manual_input',
            'users.name AS judge_name'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->count();


        $arr = [];

        foreach ($eventJudge as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventJudgeResult = new EventJudgeResult();
            $result = $eventJudgeResult->getScoreAndScaleOfJudge($arr[$i]['judge_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeCount]);
    }

    public function listAllJudgesLimit(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventJudge = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'events.title AS event_title',
            'events.date_start AS event_date_start',
            'users.name AS judge_name',
            'users.manual_input AS manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->orderBy('events.date_start', 'desc')
            ->orderBy('users.name', 'asc')
            ->get();

        $eventJudgeCount = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'events.title AS event_title',
            'users.name AS judge_name',
            'users.manual_input AS manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->count();


        $arr = [];

        foreach ($eventJudge as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventJudgeResult = new EventJudgeResult();
            $result = $eventJudgeResult->getScoreAndScaleOfJudgeByEvent($arr[$i]['judge_id'], $arr[$i]['event_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeCount]);
    }

    public function listAllAvailableJudgesLimitByEventId(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $judges = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'user_profiles.phone_no AS phone',
            'user_profiles.biography AS biography',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.id AND event_id = ' . $request->event_id . ') AS count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 5)
            ->where('manual_input', '=', 0)
            ->where(DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->distinct()
            ->get();


        $countJudges = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.id AND event_id = ' . $request->event_id . ') AS count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 5)
            ->where('manual_input', '=', 0)
            ->where(DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->distinct()
            ->count();

        $arr = [];

        foreach ($judges as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $countJudges]);
    }

    public function listAllAvailableJudgesManualLimitByEventId(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $judges = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'user_profiles.phone_no AS phone',
            'user_profiles.biography AS biography',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.id) AS count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 5)
            ->where('manual_input', '=', 1)
            ->where('status_banned', 0)
            ->where(DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->distinct()
            ->get();


        $countJudges = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.i) AS count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 5)
            ->where('manual_input', '=', 1)
            ->where('status_banned', 0)
            ->where(DB::raw('(SELECT COUNT(*) FROM event_judges WHERE judge_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->distinct()
            ->count();

        $arr = [];

        foreach ($judges as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $countJudges]);
    }

    public function listAllJudgesLimitOrder(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventJudge = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'events.title AS event_title',
            'users.name AS judge_name',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->orderBy($request->column, $request->sort)
            ->get();



        $eventJudgeCount = EventJudge::select(
            'event_judges.id AS id',
            'event_id',
            'judge_id',
            'competition_label AS judging',
            'events.title AS event_title',
            'users.name AS judge_name',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->count();


        $arr = [];

        foreach ($eventJudge as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventJudgeResult = new EventJudgeResult();
            $result = $eventJudgeResult->getScoreAndScaleOfJudgeByEvent($arr[$i]['judge_id'], $arr[$i]['event_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeCount]);
    }


    public function listJudgesEventToRate(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        // $eventMemberClass = EventMemberClass::select(
        //     'id AS event_member_class_id'
        // )
        //     ->where('event_member_classes.event_member_id', $request->event_member_id)
        //     ->offset($offset)
        //     ->limit($limit)
        //     ->get();

        // $eventMemberClassIdsArr = [];

        // foreach ($eventMemberClass as $object) {
        //     $eventMemberClassIdsArr[] = $object['event_member_class_id'];
        // }

        // $eventJudgeMemberAssignment = EventJudgeMemberAssignment::select(
        //     'event_judge_activity_id'
        // )
        //     ->whereIn('event_judge_member_assignments.event_member_class_id', $eventMemberClassIdsArr)
        //     ->offset($offset)
        //     ->limit($limit)
        //     ->get();

        // $eventJudgeMemberAssignmentIdsArr = [];

        // foreach ($eventJudgeMemberAssignment as $object) {
        //     $eventJudgeMemberAssignmentIdsArr[] = $object['event_judge_activity_id'];
        // }

        // $eventJudgeActivities = EventJudgeActivity::select(
        //     'event_judge_id',
        //     'competition_activity_id'
        // )
        //     ->whereIn('event_judge_activities.id', $eventJudgeMemberAssignmentIdsArr)
        //     ->offset($offset)
        //     ->limit($limit)
        //     ->get();

        // $eventJudgeActivitiesIdsArr = [];

        // foreach ($eventJudgeActivities as $object) {
        //     $eventJudgeActivitiesIdsArr[] = $object['event_judge_id'];
        // }


        // $eventJudge = EventJudge::select(
        //     'judge_id'
        // )
        //     ->whereIn('event_judges.id', $eventJudgeActivitiesIdsArr)
        //     ->offset($offset)
        //     ->limit($limit)
        //     ->get();

        // $eventJudgeArr = [];

        // foreach ($eventJudge as $object) {
        //     $eventJudgeArr[] = $object['judge_id'];
        // }

        $eventJudgesToRate = EventMember::select(
            'event_judge_results.id AS event_judge_results_id',

            'event_judge_activities.competition_activity_id AS event_judge_activity_competition_activity_id',
            'event_judge_activities.event_judge_id AS event_judge_activity_event_judge_id',
            'event_judges.judge_id AS event_judge_id',
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
            ->leftJoin('event_judge_results', 'event_judge_results.event_judge_member_assignment_id', '=', 'event_judge_member_assignments.id')
            ->where('event_members.member_id', $request->member_id)
            ->where('event_members.event_id', $request->event_id)
            ->where('event_member_classes.form_assessment', '<>', null)
            ->where('event_member_classes.judge_signature', '<>', null)
            ->where('event_member_classes.participant_signature', '<>', null)
            ->where('event_member_classes.grand_total', '<>', null)
            ->where('event_judge_results.score', '=', null)
            ->offset($offset)
            ->limit($limit)
            ->get();


        $eventJudgesToRateCount = EventMember::select(
            'event_judge_results.id AS event_judge_results_id',

            'event_judge_activities.competition_activity_id AS event_judge_activity_competition_activity_id',
            'event_judge_activities.event_judge_id AS event_judge_activity_event_judge_id',
            'event_judges.judge_id AS event_judge_id',
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
            ->leftJoin('event_judge_results', 'event_judge_results.event_judge_member_assignment_id', '=', 'event_judge_member_assignments.id')
            ->where('event_members.member_id', $request->member_id)
            ->where('event_members.event_id', $request->event_id)
            ->where('event_member_classes.form_assessment', '<>', null)
            ->where('event_member_classes.judge_signature', '<>', null)
            ->where('event_member_classes.participant_signature', '<>', null)
            ->where('event_member_classes.grand_total', '<>', null)
            ->where('event_judge_results.score', '=', null)
            ->count();


        $arr = [];

        foreach ($eventJudgesToRate as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgesToRateCount]);

        // $arr = [];

        // // foreach ($eventMemberClass as $object) {
        // //     $arr[] = $object->toArray();
        // // }

        // return response()->json(['eventJudgesToRate' => $eventJudgesToRate, 'eventMemberClassIdsArr' => $eventMemberClassIdsArr, 'eventJudgeMemberAssignmentIdsArr' => $eventJudgeMemberAssignmentIdsArr, 'eventJudgeActivities' => $eventJudgeActivities, 'eventJudgeActivitiesIdsArr' => $eventJudgeActivitiesIdsArr, 'eventJudgeArr' => $eventJudgeArr]);

        // $eventJudge = EventJudge::select(

        // );

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addActivity(Request $request)
    {
        $arr = json_decode($request->activities, true);

        for ($i = 0; $i < sizeof($arr); $i++) {
            $this->storeActivity($arr[$i]['activity_id'], $request->judge_id, $request->event_id);
        }

        return response()->json(['status' => 'success', 'message' => $arr], $this->successStatus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $arr = json_decode($request->judge_activities, true);

        for ($i = 0; $i < sizeof($arr); $i++) {
            $judge_id = $arr[$i]['judge_id'];

            $countEventJudge = EventJudge::select(
                'id'
            )
                ->where('event_id', '=', $request->event_id)
                ->where('judge_id', '=', $judge_id)
                ->count();

            if ($countEventJudge === 0) {
                $input['event_id'] = $request->event_id;
                $input['judge_id'] = $judge_id;

                $save = EventJudge::create($input);
            }

            $this->storeActivity($arr[$i]['activity_id'], $judge_id, $request->event_id);
        }

        return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
    }

    protected function storeActivity($arrActivity, $judge_id, $event_id)
    {
        $eventJudge = EventJudge::where('judge_id', $judge_id)->where('event_id', $event_id)->first();
        $arrCompetitionId = [];
        $competition_label = '';
        for ($j = 0; $j < sizeof($arrActivity); $j++) {
            $inputActivity['event_judge_id'] = $eventJudge->id;
            $inputActivity['competition_activity_id'] = $arrActivity[$j];

            $competitionActivity = CompetitionActivity::where('id', $arrActivity[$j])->first();

            if (!in_array($competitionActivity->competition_id, $arrCompetitionId) && $competitionActivity->competition_id !== null) {
                array_push($arrCompetitionId, $competitionActivity->competition_id);
            }

            $saveActivity = EventJudgeActivity::create($inputActivity);
        }

        for ($j = 0; $j < sizeof($arrCompetitionId); $j++) {
            $competition = Competition::where('id', $arrCompetitionId[$j])->first();
            $competition_label = $competition_label . $competition->title . ', ';
        }

        if ($competition_label !== '') {
            $competition_label = substr($competition_label, 0, -2);
        }

        $update = EventJudge::where('judge_id', $judge_id)->where('event_id', $event_id)->update(
            [
                'competition_label' => $competition_label
            ]
        );
    }

    public function storeManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'biography' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $AlreadyUser = User::where('email', $request->email)->first();

        if ($AlreadyUser !== null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'email have been used'
            ]);
        }

        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => '',
                'role_id' => 5,
                'manual_input' => 1
            ]);

            $host = \Config::get('project-config.project_host');
            $protocol = \Config::get('project-config.project_protocol');

            // STORE IMAGE
            $base64_image = $request->avatar; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            $imageName = str_random(10) . '.' . 'png';
            $path = 'public/avatar/' . $user->id . '/' . $imageName;
            Storage::disk('local')->put($path, base64_decode($file_data));
            $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $storageURL = Storage::url($path);
            $imageURL = $protocol . '://' . $host . '/public' . $storageURL;


            $userProfile = UserProfile::create([
                'user_id' => $user->id,
                'avatar' => $imageURL,
                'phone_no' => $request->phone,
                'biography' => $request->biography
            ]);


            DB::commit();
            // all good

            return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } catch (\Exception $e) {

            return response()->json(['status' => 'failed', 'message' => 'create failed', 'error' => $e], 401);

            DB::rollback();
            // something went wrong
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventJudge $eventJudge)
    {
        $countEventJudge = EventJudge::select('id', 'event_id', 'judge_id')
            ->where('id', $request->id)
            ->count();

        $countEventJudgeResult = EventJudgeResult::select(
            'id'
        )
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_results.event_judge_activity_id')
            ->where('event_judge_activities.event_judge_id', '=', $request->id)
            ->count();

        $countEventMemberResult = EventMemberResult::select(
            'id'
        )
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_member_results.event_judge_activity_id')
            ->where('event_judge_activities.event_judge_id', '=', $request->id)
            ->count();

        $countJudgeMemberAssignment = EventJudgeMemberAssignment::select(
            'id'
        )
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->where('event_judge_activities.event_judge_id', '=', $request->id)
            ->count();

        if ($countEventJudge > 0) {
            if ($countEventJudgeResult > 0) {
                return response()->json(['status' => 'failed', 'message' => 'cannot update judge who has been rated'], 200);
            }
            if ($countEventMemberResult > 0) {
                return response()->json(['status' => 'failed', 'message' => 'cannot update judge who has rated the participant'], 200);
            }
            if ($countJudgeMemberAssignment > 0) {
                return response()->json(['status' => 'failed', 'message' => 'cannot update judge who has been assigned with participant'], 200);
            }

            $delete = EventJudgeActivity::where('event_judge_id', $request->id)->delete();

            $arr = json_decode($request->activities, true);
            $arrCompetitionId = [];
            $competition_label = '';

            for ($i = 0; $i < sizeof($arr); $i++) {
                $competition_activity_id = (int) $arr[$i]['id'];

                $input['event_judge_id'] = $request->id;
                $input['competition_activity_id'] = $competition_activity_id;

                $competitionActivity = CompetitionActivity::where('id', $competition_activity_id)->first();

                if (!in_array($competitionActivity->competition_id, $arrCompetitionId) && $competitionActivity->competition_id !== null) {
                    array_push($arrCompetitionId, $competitionActivity->competition_id);
                }

                $saveActivity = EventJudgeActivity::create($input);
            }


            for ($j = 0; $j < sizeof($arrCompetitionId); $j++) {
                $competition = Competition::where('id', $arrCompetitionId[$j])->first();
                $competition_label = $competition_label . $competition->title . ', ';
            }

            if ($competition_label !== '') {
                $competition_label = substr($competition_label, 0, -2);
            }

            $update = EventJudge::where('id', $request->id)->update(
                [
                    'competition_label' => $competition_label
                ]
            );

            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event judge not found'], 200);
        }
    }

    public function updateJudgeManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'biography' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $AlreadyUser = User::where('email', $request->email)->where('id', '<>', $request->user_id)->first();

        if ($AlreadyUser !== null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'email have been used'
            ]);
        }

        DB::beginTransaction();

        try {

            $userUpdate = User::where('id', $request->user_id)->update(
                [
                    'name' => $request->name,
                    'email' => $request->email
                ]
            );

            if ($request->avatar != null) {
                $host = \Config::get('project-config.project_host');
                $protocol = \Config::get('project-config.project_protocol');

                // STORE IMAGE
                $base64_image = $request->avatar; // your base64 encoded
                @list($type, $file_data) = explode(';', $base64_image);
                @list(, $file_data) = explode(',', $file_data);

                $imageName = str_random(10) . '.' . 'png';
                $path = 'public/avatar/' . $request->user_id . '/' . $imageName;
                Storage::disk('local')->put($path, base64_decode($file_data));
                $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
                $storageURL = Storage::url($path);
                $imageURL = $protocol . '://' . $host . '/public' . $storageURL;


                $userProfileUpdate = UserProfile::where('user_id', $request->user_id)->update(
                    [
                        'avatar' => $imageURL,
                        'phone_no' => $request->phone,
                        'biography' => $request->biography
                    ]
                );
            }

            $userProfileUpdate = UserProfile::where('user_id', $request->user_id)->update(
                [
                    'phone_no' => $request->phone,
                    'biography' => $request->biography
                ]
            );

            DB::commit();
            // all good

            return response()->json(['status' => 'success', 'message' => 'update successfull'], $this->successStatus);
        } catch (\Exception $e) {

            return response()->json(['status' => 'failed', 'message' => 'update failed', 'error' => $e], 401);

            DB::rollback();
            // something went wrong
        }
    }


    public function delete(Request $request, EventJudge $eventJudge)
    {
        $countEventJudgeResult = EventJudgeResult::select(
            'event_judge_results.id AS id'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_judge_results.event_judge_member_assignment_id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->where('event_judge_activities.event_judge_id', '=', $request->id)
            ->count();

        $countEventMemberResult = EventMemberResult::select(
            'event_member_results.id AS id'
        )
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_member_results.event_judge_member_assignment_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->where('event_member_classes.event_member_id', '=', $request->id)
            ->count();

        $countJudgeMemberAssignment = EventJudgeMemberAssignment::select(
            'id'
        )
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->where('event_judge_activities.event_judge_id', '=', $request->id)
            ->count();

        if ($countEventJudgeResult > 0) {
            return response()->json(['status' => 'failed', 'message' => 'cannot delete judge who has been rated'], 200);
        }
        if ($countEventMemberResult > 0) {
            return response()->json(['status' => 'failed', 'message' => 'cannot delete judge who has rated the participant'], 200);
        }
        // if ($countJudgeMemberAssignment > 0) {
        //     return response()->json(['status' => 'failed', 'message' => 'cannot delete judge who has been assigned with participant'], 200);
        // }

        Schema::disableForeignKeyConstraints();

        if ($countJudgeMemberAssignment > 0) {
            $deleteJudgeAssignment = EventJudgeMemberAssignment::join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                ->where('event_judge_activities.event_judge_id', '=', $request->id)
                ->delete();
        }

        $deleteActivity = EventJudgeActivity::where('event_judge_id', $request->id)->delete();

        $delete = EventJudge::where('id', $request->id)->delete();
        if ($delete) {
            $success = ['status' => 'success', 'message' => 'deleted successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'delete failed'];
        }

        Schema::enableForeignKeyConstraints();

        return response()->json($success, $this->successStatus);
    }
}
