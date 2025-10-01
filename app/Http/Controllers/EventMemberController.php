<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventMember;
use App\EventMemberResult;
use App\Competition;
use App\User;
use App\EventWinner;
use App\EventMemberClass;
use App\EventJudge;
use App\EventMemberAssignment;
use App\CanQScore;
use App\CanLoudScore;
use App\CanJamScoreHistory;
use App\CanCraftScore;
use App\CanCraftProExtreme;
use App\CanTuneConsumerPyramid;
use App\CanTuneProsumerPyramid;
use App\CanTuneProfessionalPyramid;
use App\CanPerformScore;
use App\CanDanceScore;
use App\Car;
use Illuminate\Support\Facades\Schema;
use App\EventJudgeResult;
use App\CompetitionActivity;
use App\EventJudgeMemberAssignment;
use App\Event;
use App\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventMemberController extends Controller
{
    public $successStatus = 200;

    public function listCanQPointAllParticipants(Request $request)
    {
        $user = User::select(
            'users.id AS user_id',
            'users.name AS user_name',
            'users.can_q_consumer_point AS can_q_consumer_point',
            'users.can_q_prosumer_point AS can_q_prosumer_point',
            'users.can_q_professional_point AS can_q_professional_point',
            'user_profiles.avatar AS user_avatar'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 6)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('users.can_q_consumer_point', 'desc')
            ->orderBy('users.can_q_prosumer_point', 'desc')
            ->orderBy('users.can_q_professional_point', 'desc')
            ->orderBy('users.name', 'asc')
            ->get();

        $userCount = User::select(
            'users.id AS user_id',
            'users.name AS user_name',
            'users.can_q_consumer_point AS can_q_consumer_point',
            'users.can_q_prosumer_point AS can_q_prosumer_point',
            'users.can_q_professional_point AS can_q_professional_point',
            'user_profiles.avatar AS user_avatar'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->where('users.role_id', '=', 6)
            ->count();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $userCount]);
    }

    public function listDetail(Request $request)
    {
        $eventMember = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'competition_label AS competition',
            'events.title AS event_title',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_members.member_id', '=', $request->member_id)
            ->get();

        $competitions = new EventMember();
        $eventMember[0]['competitions'] = $competitions->getCompetitionsByEventIdAndMemberId($request->event_id, $request->member_id);

        return response()->json(['data' => $eventMember], 200);
    }

    public function listMembersOfEventLimit(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventMember = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'event_members.created_at',
            'events.title AS event_title',
            'users.name AS member_name',
            'users.role_id AS role_id',
            // 'users.manual_input AS member_manual_input',
            'user_profiles.avatar AS avatar',
            'competition_label AS competition'
            // 'event_member_classes.judge_signature AS judge_signature',
            // 'event_member_classes.participant_signature AS participant_signature'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            // ->leftJoin('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.member_id')
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('event_members.competition_label', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->distinct()
            ->get();

        $eventMemberCount = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'users.name AS member_name',
            'event_members.created_at',
            'competitions.title AS competition'
            // 'event_member_classes.judge_signature AS judge_signature',
            // 'event_member_classes.participant_signature AS participant_signature'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            // ->leftJoin('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.member_id')
            ->where('event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('event_members.competition_label', 'like', '%' . $request->search . '%');
            })
            ->distinct('event_members.id')
            ->count('event_members.id');


        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventMemberResult = new EventMemberResult();
            $result = $eventMemberResult->getScoreAndScaleOfMember($arr[$i]['member_id']);
            // $eventMember = new EventMember();
            // $status = $eventMember->getMemberStatusAssignOfEventAndClass($arr[$i]['id']);
            // getClassAndStatusAssignOfMember
            $eventMemberClass = new EventMemberClass();
            $status_class = $eventMemberClass->getActivityClassAssignOfMember($arr[$i]['event_id'], $arr[$i]['member_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
            $arr[$i]['activity_classes'] = $status_class;
            // $arr[$i]['status'] = $status;
        }

        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listMembersOfEventLimitOrder(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventMember = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'events.title AS event_title',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('event_members.competition_label', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventMemberCount = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'users.name AS member_name',
            'competitions.title AS competition'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where('event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('event_members.competition_label', 'like', '%' . $request->search . '%');
            })
            ->count();


        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventMemberResult = new EventMemberResult();
            $result = $eventMemberResult->getScoreAndScaleOfMember($arr[$i]['member_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
        }

        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listAllMembersLimit(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventMember = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'competition_label AS competition',
            'events.title AS event_title',
            'events.date_start AS event_date_start',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
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

        $eventMemberCount = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'competition_label AS competition',
            'events.title AS event_title',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventMemberResult = new EventMemberResult();
            $result = $eventMemberResult->getScoreAndScaleOfMemberByEvent($arr[$i]['member_id'], $arr[$i]['event_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
        }

        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listAllMembersLimitByUserId(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventMember = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'competition_label AS competition',
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.location AS location',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where('users.id', '=', $request->user_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->get();

        $eventMemberCount = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'competition_label AS competition',
            'events.title AS event_title',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where('users.id', '=', $request->user_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventMemberResult = new EventMemberResult();
            $result = $eventMemberResult->getScoreAndScaleOfMember($arr[$i]['member_id']);
            $event = new Event();
            $statusPast = $event->getEventStatusPastByDateStart($arr[$i]['date_start']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
            $arr[$i]['status_past'] = $statusPast;
        }

        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listAllMembersLimitOrder(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventMember = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'competition_label AS competition',
            'events.title AS event_title',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventMemberCount = EventMember::select(
            'event_members.id AS id',
            'event_id',
            'member_id',
            'competition_label AS competition',
            'events.title AS event_title',
            'users.name AS member_name',
            'users.role_id AS role_id',
            'user_profiles.avatar AS avatar'
        )
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('competition_label', 'like', '%' . $request->search . '%')
                    ->orWhere('events.title', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $eventMemberResult = new EventMemberResult();
            $result = $eventMemberResult->getScoreAndScaleOfMemberByEvent($arr[$i]['member_id'], $arr[$i]['event_id']);
            $arr[$i]['score'] = $result['score'];
            $arr[$i]['scale'] = $result['scale'];
        }

        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listMembersEventToRate(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $eventMembersToRate = EventJudge::select(
            'event_member_results.id AS event_member_result_id',

            // 'event_judge_activities.competition_activity_id AS event_judge_activity_competition_activity_id',
            // 'event_judge_activities.event_judge_id AS event_judge_activity_event_judge_id',
            'event_members.member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS avatar',

            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',

            'class_grades.name AS class_grade_name',
            'class_groups.name AS class_group_name',

            'event_judges.id AS event_judge_id',
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.grand_total AS grand_total',
            'event_judge_member_assignments.event_member_class_id AS event_judge_member_assignment_event_member_class_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id'
        )
            ->join('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->join('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('event_member_results', 'event_member_results.event_judge_member_assignment_id', '=', 'event_judge_member_assignments.id')
            ->where('event_judges.judge_id', $request->judge_id)
            ->where('event_judges.event_id', $request->event_id)
            ->where('event_member_classes.form_assessment', '<>', null)
            ->where('event_member_classes.judge_signature', '<>', null)
            ->where('event_member_classes.participant_signature', '<>', null)
            ->where('event_member_classes.grand_total', '<>', null)
            ->where('event_member_results.score', '=', null)
            ->offset($offset)
            ->limit($limit)
            ->get();


        $eventMembersToRateCount = EventJudge::select(
            'event_member_results.id AS event_member_result_id',

            // 'event_judge_activities.competition_activity_id AS event_judge_activity_competition_activity_id',
            // 'event_judge_activities.event_judge_id AS event_judge_activity_event_judge_id',
            'event_members.member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS avatar',

            'competition_activities.id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name',

            'class_grades.name AS class_grade_name',
            'class_groups.name AS class_group_name',

            'event_judges.id AS event_judge_id',
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.grand_total AS grand_total',
            'event_judge_member_assignments.event_member_class_id AS event_judge_member_assignment_event_member_class_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id'
        )
            ->join('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->join('competition_activities', 'competition_activities.id', '=', 'event_judge_activities.competition_activity_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->join('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('event_member_results', 'event_member_results.event_judge_member_assignment_id', '=', 'event_judge_member_assignments.id')
            ->where('event_judges.judge_id', $request->judge_id)
            ->where('event_judges.event_id', $request->event_id)
            ->where('event_member_classes.form_assessment', '<>', null)
            ->where('event_member_classes.judge_signature', '<>', null)
            ->where('event_member_classes.participant_signature', '<>', null)
            ->where('event_member_classes.grand_total', '<>', null)
            ->where('event_member_results.score', '=', null)
            ->count();


        $arr = [];

        foreach ($eventMembersToRate as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventMembersToRateCount]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $event = Event::where('id', $request->event_id)->first();

        if ($event) {
            $status_can_final = (int) $event->status_can_final;

            if ($status_can_final === 1) {
                $competition_activity_id = (int) $request->competition_activity_id;
                $event = $request->event_id;
                $member_id = $request->member_id;
                $class_grade_id = $request->class_grade_id;

                $eventMemberClass = new EventMemberClass();

                if ($competition_activity_id === 1) {
                    $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnActivity($member_id, $competition_activity_id);

                    if ($isCompeteBefore === true) {
                        $isTopSixteenPointRank = $eventMemberClass->isMemberIncludedOnTopSixteenPointRank($member_id, $class_grade_id);

                        if ($isTopSixteenPointRank === false) {
                            return response()->json(['status' => 'failed', 'message' => 'Only 16 high-ranking participants could register in this CAN Final Event'], 200);
                        }
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'participant must compete once on CAN Q before this CAN Final Event'], 200);
                    }
                } else if ($competition_activity_id === 2) {
                    // $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnCanLoud($member_id);
                    $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnActivity($member_id, $competition_activity_id);

                    if ($isCompeteBefore === false) {
                        return response()->json(['status' => 'failed', 'message' => 'participant must compete once on CAN Loud before this CAN Final Event'], 200);
                    }
                } else if ($competition_activity_id === 4) {
                    // $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnCanCraft($member_id);
                    $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnActivity($member_id, $competition_activity_id);

                    if ($isCompeteBefore === false) {
                        return response()->json(['status' => 'failed', 'message' => 'participant must compete once on CAN Craft before this CAN Final Event'], 200);
                    }
                } else if ($competition_activity_id === 5) {
                    // $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnCanTune($member_id);
                    $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnActivity($member_id, $competition_activity_id);

                    if ($isCompeteBefore === false) {
                        return response()->json(['status' => 'failed', 'message' => 'participant must compete once on CAN Tune before this CAN Final Event'], 200);
                    }
                } else if ($competition_activity_id === 6) {
                    // $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnCanPerform($member_id);
                    $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnActivity($member_id, $competition_activity_id);

                    if ($isCompeteBefore === false) {
                        return response()->json(['status' => 'failed', 'message' => 'participant must compete once on CAN Perform before this CAN Final Event'], 200);
                    }
                } else if ($competition_activity_id === 7) {
                    // $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnCanDance($member_id);
                    $isCompeteBefore = $eventMemberClass->isMemberHaveCompeteOnceOnActivity($member_id, $competition_activity_id);

                    if ($isCompeteBefore === false) {
                        return response()->json(['status' => 'failed', 'message' => 'participant must compete once on CAN Dance before this CAN Final Event'], 200);
                    }
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }

        $eventMember = EventMember::select(
            'id',
            'event_id',
            'member_id'
        )
            ->where('event_id', '=', $request->event_id)
            ->where('member_id', '=', $request->member_id)
            ->get();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }

        // $eventMemberCount = $eventMember->count();

        // if ($eventMemberCount > 0) {
        //     for ($i = 0; $i < sizeof($arr); $i++) {
        //         $event_member_id = $arr[$i]['id'];

        // $eventMemberClassCount = EventMemberClass::select(
        //     'id'
        // )
        //     ->where('event_member_id', '=', $event_member_id)
        //     ->where('competition_activity_id', '=', $request->competition_activity_id)
        //     ->where('class_group_id', '=', $request->class_group_id)
        //     ->where('class_grade_id', '=', $request->class_grade_id)
        //     ->count();

        // if ($eventMemberClassCount > 0) {
        //     return response()->json(['status' => 'failed', 'message' => 'this participant have been registered in this activity and class'], 200);
        // }

        // if ($request->competition_activity_id === 5) {
        //     $eventMemberCanTune = EventMemberClass::select(
        //         'id'
        //     )
        //         ->where('event_member_id', '=', $event_member_id)
        //         ->where('competition_activity_id', '=', $request->competition_activity_id)
        //         ->where('class_grade_id', '=', $request->class_grade_id)
        //         ->count();
        //
        //     if ($eventMemberCanTune > 0) {
        //         return response()->json(['status' => 'failed', 'message' => 'this participant have been registered in this activity'], 200);
        //     }
        // }
        //     }
        // }

        $user = User::where('id', $request->member_id)->first();

        if ($user) {
            $role_id = (int) $user->role_id;

            if ($role_id !== 4 && $role_id !== 6) {
                return response()->json(['status' => 'failed', 'message' => 'user was not Participant or CAN Friend'], 200);
            }

            $update = User::where('id', $request->member_id)->update(
                [
                    'role_id' => 6
                ]
            );

            $eventMember = EventMember::where('event_id', '=', $request->event_id)->where('member_id', '=', $request->member_id)->first();

            if ($eventMember) {
                return $this->saveEventMemberClass($request);
            } else {
                $inputEventMember['event_id'] = $request->event_id;
                $inputEventMember['member_id'] = $request->member_id;

                $save = EventMember::create($inputEventMember);

                if ($save) {
                    // return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
                    return $this->saveEventMemberClass($request);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }
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
                'role_id' => 6,
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

            // $car = Car::create([
            //     'user_id' => $user->id,
            //     'vehicle' => $request->vehicle,
            //     'license_plate' => $request->license_plate,
            //     'color' => $request->color,
            //     'engine' => 0,
            //     'power' => 0,
            //     'seat' => 0,
            //     'transmission_type' => 'Manual',
            //     'vin_number' => '',
            //     'type' => '',
            //     'manual_input' => 1
            // ]);

            DB::commit();
            // all good

            return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } catch (\Exception $e) {

            return response()->json(['status' => 'failed', 'message' => 'create failed', 'error' => $e], 401);

            DB::rollback();
            // something went wrong
        }
    }

    public function storeCarManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'vehicle' => 'required',
            'license_plate' => 'required',
            'color' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $car = Car::create([
            'user_id' => $request->user_id,
            'vehicle' => $request->vehicle,
            'license_plate' => $request->license_plate,
            'color' => $request->color,
            'engine' => 0,
            'power' => 0,
            'seat' => 0,
            'transmission_type' => 'Manual',
            'vin_number' => '',
            'type' => '',
            'manual_input' => 1
        ]);

        if ($car) {
            return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    protected function saveEventMemberClass(Request $request)
    {
        $eventMember = EventMember::where('event_id', '=', $request->event_id)->where('member_id', '=', $request->member_id)->first();

        $inputEventMemberClass['total_participants'] = $request->total_participants;
        $inputEventMemberClass['assessed'] = $request->assessed;
        $inputEventMemberClass['event_member_id'] = $eventMember->id;
        $inputEventMemberClass['competition_activity_id'] = $request->competition_activity_id;
        $inputEventMemberClass['class_group_id'] = $request->class_group_id;
        $inputEventMemberClass['class_grade_id'] = $request->class_grade_id;
        $inputEventMemberClass['car_id'] = $request->car_id;
        $inputEventMemberClass['studio_info'] = $request->studio_info;
        $inputEventMemberClass['gear'] = $request->gear;
        $inputEventMemberClass['team_name'] = $request->team_name;

        $save = EventMemberClass::create($inputEventMemberClass);

        if ($save) {
            if ($request->can_craft_pro_extreme_status === 1) {
                return $this->storeProExtremeItems($request, $eventMember->id);
            } else {
                return $this->updateCompetitionLabel($request);
            }
            // return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    protected function storeProExtremeItems(Request $request, $eventMemberId)
    {
        $eventMemberClass = EventMemberClass::where('event_member_id', $eventMemberId)->where('competition_activity_id', $request->competition_activity_id)
            ->where('class_group_id', $request->class_group_id)->first();

        $arrItemProExtreme = json_decode($request->pro_extreme_items, true);

        if (sizeof($arrItemProExtreme) > 0) {
            for ($i = 0; $i < sizeof($arrItemProExtreme); $i++) {
                $input['event_member_class_id'] = $eventMemberClass->id;
                $input['items'] = $arrItemProExtreme[$i]['items'];
                $input['items'] = $arrItemProExtreme[$i]['items'];
                $input['comment_participant'] = $arrItemProExtreme[$i]['comment_participant'];

                $save = CanCraftProExtreme::create($input);
            }
            return $this->updateCompetitionLabel($request);
        } else {
            return $this->updateCompetitionLabel($request);
        }
    }

    protected function updateCompetitionLabel(Request $request)
    {
        $eventMember = EventMember::where('event_id', '=', $request->event_id)->where('member_id', '=', $request->member_id)->first();

        $eventMemberClass = EventMemberClass::select('competition_activity_id')->where('event_member_id', '=', $eventMember->id)->get();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        $competitionLabel = '';
        $arrCompetitionId = [];

        for ($i = 0; $i < sizeof($arr); $i++) {
            $competitionActivityId = (int) $arr[$i]['competition_activity_id'];

            $competitionActivity = CompetitionActivity::select('competition_id')->where('id', '=', $competitionActivityId)->get();

            $arrCompetitionActivity = [];
            foreach ($competitionActivity as $object) {
                $arrCompetitionActivity[] = $object->toArray();
            }

            for ($j = 0; $j < sizeof($arrCompetitionActivity); $j++) {
                $competitionId = (int) $arrCompetitionActivity[$j]['competition_id'];

                if (!in_array($competitionId, $arrCompetitionId)) {
                    array_push($arrCompetitionId, $competitionId);
                }
            }
        }

        // return response()->json(['status' => 'success', 'arrCompetitionId' => $arrCompetitionId, 'competitionLabel' => $competitionLabel, 'eventMember->id' => $eventMember->id], $this->successStatus);



        for ($i = 0; $i < sizeof($arrCompetitionId); $i++) {
            $competition = Competition::where('id', $arrCompetitionId[$i])->first();
            // return response()->json(['status' => 'success', 'competition' => $competition, 'eventMember' => $eventMember], $this->successStatus);
            $competition_title = $competition->title;
            $competitionLabel = $competitionLabel . $competition_title . ', ';
        }

        // return response()->json(['status' => 'success', 'competitionLabel' => $competitionLabel, 'eventMember->id' => $eventMember->id], $this->successStatus);

        if ($competitionLabel !== '') {
            $competitionLabel = substr($competitionLabel, 0, -2);
        }



        $update = EventMember::where('id', $eventMember->id)->update(
            [
                'competition_label' => $competitionLabel
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);

        // if ($update) {
        //     return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        // } else {
        //     return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        // }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventMember $eventMember)
    {
        $eventMember = EventMember::where('event_id', $request->event_id)->where('member_id', $request->member_id)->first();

        $countEventMemberResult = EventMemberResult::select(
            'id'
        )
            ->where('event_member_id', $eventMember->id)
            ->count();

        if ($countEventMemberResult > 0) {
            return response()->json(['status' => 'failed', 'message' => 'cannot update participant that has been rated'], 200);
        }

        $eventMember = EventMember::select(
            'id',
            'event_id',
            'member_id'
        )
            ->where('event_id', '=', $request->event_id)
            ->where('member_id', '=', $request->member_id)
            ->get();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }

        $eventMemberCount = $eventMember->count();

        if ($eventMemberCount > 0) {
            for ($i = 0; $i < sizeof($arr); $i++) {
                $event_member_id = $arr[$i]['id'];

                // $eventMemberClassCount = EventMemberClass::select(
                //     'id'
                // )
                //     ->where('event_member_id', '=', $event_member_id)
                //     ->where('competition_activity_id', '=', $request->competition_activity_id)
                //     ->where('class_group_id', '=', $request->class_group_id)
                //     ->where('class_grade_id', '=', $request->class_grade_id)
                //     ->count();
                //
                // if ($eventMemberClassCount > 0) {
                //     return response()->json(['status' => 'failed', 'message' => 'this participant have been registered in this activity and class'], 200);
                // }

                $eventMemberAssignmentCount = EventJudgeMemberAssignment::select('event_judge_member_assignments.id')
                    ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
                    ->where('event_member_classes.event_member_id', '=', $event_member_id)->count();

                if ($eventMemberAssignmentCount > 0) {
                    return response()->json(['status' => 'failed', 'message' => 'this participant have been assign to judge'], 200);
                }
            }

            return $this->updateEventMemberClass($request);
        } else {
            return $this->updateEventMemberClass($request);
        }
    }

    protected function updateEventMemberClass(Request $request)
    {
        $eventMember = EventMember::where('event_id', '=', $request->event_id)->where('member_id', '=', $request->member_id)->first();

        $updateEventMemberClass['total_participants'] = $request->total_participants;
        $updateEventMemberClass['assessed'] = $request->assessed;
        $updateEventMemberClass['event_member_id'] = $eventMember->id;
        $updateEventMemberClass['competition_activity_id'] = $request->competition_activity_id;
        $updateEventMemberClass['class_group_id'] = $request->class_group_id;
        $updateEventMemberClass['class_grade_id'] = $request->class_grade_id;
        $updateEventMemberClass['car_id'] = $request->car_id;
        $updateEventMemberClass['studio_info'] = $request->studio_info;
        $updateEventMemberClass['gear'] = $request->gear;
        $updateEventMemberClass['team_name'] = $request->team_name;

        $update = EventMemberClass::where('id', $request->event_member_class_id)->update(
            [
                'total_participants' => $updateEventMemberClass['total_participants'],
                'assessed' => $updateEventMemberClass['assessed'],
                'event_member_id' => $updateEventMemberClass['event_member_id'],
                'competition_activity_id' => $updateEventMemberClass['competition_activity_id'],
                'class_group_id' => $updateEventMemberClass['class_group_id'],
                'class_grade_id' => $updateEventMemberClass['class_grade_id'],
                'car_id' => $updateEventMemberClass['car_id'],
                'studio_info' => $updateEventMemberClass['studio_info'],
                'gear' => $updateEventMemberClass['gear'],
                'team_name' => $updateEventMemberClass['team_name']
            ]
        );

        if ($update) {
            $this->updateCompetitionLabel($request);
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    public function updateScore(Request $request, EventMember $eventMember)
    {
        // $eventMember = EventMember::where('event_id', '=', $request->event_id)->where('member_id', '=', $request->member_id)->first();

        $updateEventMemberClass['grand_total'] = $request->score;
        // $updateEventMemberClass['assessed'] = $request->assessed;
        // $updateEventMemberClass['event_member_id'] = $eventMember->id;
        // $updateEventMemberClass['competition_activity_id'] = $request->competition_activity_id;
        // $updateEventMemberClass['class_group_id'] = $request->class_group_id;
        // $updateEventMemberClass['class_grade_id'] = $request->class_grade_id;
        // $updateEventMemberClass['car_id'] = $request->car_id;
        // $updateEventMemberClass['studio_info'] = $request->studio_info;
        // $updateEventMemberClass['gear'] = $request->gear;
        // $updateEventMemberClass['team_name'] = $request->team_name;

        $update = EventMemberClass::where('id', $request->event_member_class_id)->update(
            [
                'grand_total' => $updateEventMemberClass['grand_total'],
                // 'assessed' => $updateEventMemberClass['assessed'],
                // 'event_member_id' => $updateEventMemberClass['event_member_id'],
                // 'competition_activity_id' => $updateEventMemberClass['competition_activity_id'],
                // 'class_group_id' => $updateEventMemberClass['class_group_id'],
                // 'class_grade_id' => $updateEventMemberClass['class_grade_id'],
                // 'car_id' => $updateEventMemberClass['car_id'],
                // 'studio_info' => $updateEventMemberClass['studio_info'],
                // 'gear' => $updateEventMemberClass['gear'],
                // 'team_name' => $updateEventMemberClass['team_name']
            ]
        );

        if ($update) {
            // $this->updateCompetitionLabel($request);
            return response()->json(['status' => 'success', 'message' => 'updated score successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update score failed'], 401);
        }
    }

    public function updateMemberManual(Request $request)
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

    public function updateCar(Request $request)
    {
        $carUpdate['vehicle'] = $request->vehicle;
        $carUpdate['license_plate'] = $request->license_plate;
        $carUpdate['color'] = $request->color;

        $update = Car::where('id', $request->id)->update(
            [
                // 'avatar' => $carUpdate['avatar'],
                // 'front_car_image' => $carUpdate['front_car_image'],
                // 'engine' => $carUpdate['engine'],
                // 'power' => $carUpdate['power'],
                // 'seat' => $carUpdate['seat'],
                // 'transmission_type' => $carUpdate['transmission_type'],
                'vehicle' => $carUpdate['vehicle'],
                'license_plate' => $carUpdate['license_plate'],
                // 'vin_number' => $carUpdate['vin_number'],
                // 'type' => $carUpdate['type'],
                'color' => $carUpdate['color'],
                // 'headunits' => $carUpdate['headunits'],
                // 'processor' => $carUpdate['processor'],
                // 'power_amplifier' => $carUpdate['power_amplifier'],
                // 'speakers' => $carUpdate['speakers'],
                // 'wires' => $carUpdate['wires'],
                // 'other_devices' => $carUpdate['other_devices'],
                // 'signal_flowchart' => $carUpdate['signal_flowchart'],
                // 'power_supply_flowchart' => $carUpdate['power_supply_flowchart']
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    public function delete(Request $request, EventMember $eventMember)
    {
        $eventMemberClass = EventMemberClass::select('id')
            ->where('event_member_id', $request->event_member_id)
            ->get();

        $eventMemberClassCount = EventMemberClass::select('id')
            ->where('event_member_id', $request->event_member_id)
            ->count();

        $eventMemberAssignmentCount = EventJudgeMemberAssignment::select('event_judge_member_assignments.id AS id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.event_member_id', $request->event_member_id)
            ->count();

        $eventMemberResultCount = EventMemberResult::select('event_member_results.id AS id')
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_member_results.event_judge_member_assignment_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.event_member_id', $request->event_member_id)
            ->count();

        $eventJudgeResultCount = EventJudgeResult::select('event_judge_results.id AS id')
            ->join('event_judge_member_assignments', 'event_judge_member_assignments.id', '=', 'event_judge_results.event_judge_member_assignment_id')
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.event_member_id', $request->event_member_id)
            ->count();

        if ($eventMemberResultCount > 0) {
            return response()->json(['status' => 'failed', 'message' => 'can not remove participant because this participant have been rated in this event'], 200);
        } else if ($eventJudgeResultCount > 0) {
            return response()->json(['status' => 'failed', 'message' => 'can not remove participant because this participant have rate judge in this event'], 200);
        } else {
            Schema::disableForeignKeyConstraints();

            if ($eventMemberClassCount > 0) {
                $arrMemberClass = [];

                foreach ($eventMemberClass as $object) {
                    $arrMemberClass[] = $object->toArray();
                }

                for ($i = 0; $i < sizeof($arrMemberClass); $i++) {
                    $eventMemberClassId = $arrMemberClass[$i]['id'];

                    $canQScore = CanQScore::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canLoudScore = CanLoudScore::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canJamScoreHistory = CanJamScoreHistory::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canCraftScore = CanCraftScore::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canCraftProExtreme = CanCraftProExtreme::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canTuneConsumerPyramid = CanTuneConsumerPyramid::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canTuneProsumerPyramid = CanTuneProsumerPyramid::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canTuneProfessionalPyramid = CanTuneProfessionalPyramid::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canPerformScore = CanPerformScore::select('id')->where('event_member_class_id', $eventMemberClassId)->count();
                    $canDanceScore = CanDanceScore::select('id')->where('event_member_class_id', $eventMemberClassId)->count();

                    if (
                        $canQScore > 0 || $canLoudScore > 0 || $canJamScoreHistory > 0 || $canCraftScore > 0 || $canCraftProExtreme > 0 || $canTuneConsumerPyramid > 0
                        || $canTuneProsumerPyramid > 0 || $canTuneProfessionalPyramid > 0 || $canPerformScore > 0 || $canDanceScore > 0
                    ) {
                        return response()->json(['status' => 'failed', 'message' => 'can not remove participant because this participant have been assessed'], 200);
                    }
                }

                $deleteEventJudgeMemberAssignment = EventJudgeMemberAssignment::join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
                    ->where('event_member_classes.event_member_id', $request->event_member_id)->delete();

                $deleteEventMemberClass = EventMemberClass::where('event_member_id', $request->event_member_id)->delete();
            }


            $delete = EventMember::where('id', $request->event_member_id)->delete();
            if ($delete) {
                $success = ['status' => 'success', 'message' => 'deleted successfully'];
            } else {
                $success = ['status' => 'failed', 'message' => 'delete failed', 'event_member_id' => $request->event_member_id];
            }

            Schema::enableForeignKeyConstraints();

            return response()->json($success, $this->successStatus);
        }
    }

    public function listAllAvailableParticipantsLimitByEventId(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $members = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'user_profiles.biography AS biography',
            'user_profiles.phone_no AS phone',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id AND event_id = ' . $request->event_id . ') AS count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            // ->where('users.role_id', '=', 6)
            ->whereIn('users.role_id', [4, 6])
            ->where('manual_input', '=', 0)
            // ->where('users.email', '!=', '')->orWhereNull('users.email')
            // ->where('users.password', '!=', '')
            // ->where(DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->distinct()
            ->get();


        $countMembers = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id AND event_id = ' . $request->event_id . ') AS count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            // ->where('users.role_id', '=', 6)
            ->whereIn('users.role_id', [4, 6])
            ->where('manual_input', '=', 0)
            // ->where('users.email', '!=', '')
            // ->where('users.password', '!=', '')
            // ->where(DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->distinct()
            ->count();

        $arr = [];

        foreach ($members as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $countMembers]);
    }


    public function listAllAvailableParticipantsManualLimitByEventId(Request $request)
    {
        $offset = (int) $request->offset;
        $limit = (int) $request->limit;

        $members = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'user_profiles.biography AS biography',
            'user_profiles.phone_no AS phone',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id) AS registered_count')
            // DB::raw('(SELECT COUNT(*) FROM event_member_classes WHERE event_member_classes.event_member_id = e_member_id AND event_member_classes.grand_total IS NOT NULL) AS event_member_class_count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            // ->where('users.role_id', '=', 6)
            ->whereIn('users.role_id', [4, 6])
            ->where('manual_input', '=', 1)
            ->where('status_banned', 0)
            // ->where('users.email', '=', '')
            // ->where('users.password', '=', '')
            // ->where(DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->offset($offset)
            ->limit($limit)
            ->distinct()
            ->get();

        $countMembers = User::select(
            'users.id AS id',
            'user_profiles.avatar AS avatar',
            'users.name AS name',
            'users.email AS email',
            'users.manual_input AS manual_input',
            DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id AND event_id = ' . $request->event_id . ') AS count')
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            // ->where('users.role_id', '=', 6)
            ->whereIn('users.role_id', [4, 6])
            ->where('manual_input', '=', 1)
            ->where('status_banned', 0)
            // ->where('users.email', '=', '')
            // ->where('users.password', '=', '')
            // ->where(DB::raw('(SELECT COUNT(*) FROM event_members WHERE member_id = users.id AND event_id = ' . $request->event_id . ')'), '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('user_profiles.phone_no', 'like', '%' . $request->search . '%');
            })
            ->distinct()
            ->count();

        $arr = [];

        foreach ($members as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $countMembers]);
    }
}
