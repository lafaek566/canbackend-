<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventMemberClass;
use App\EventJudgeActivity;
use App\EventJudgeMemberAssignment;
use App\EventMember;
use function GuzzleHttp\json_decode;
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
use App\EventActivityClassForm;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EventJudgeMemberAssignmentController extends Controller
{
    public $successStatus = 200;

    public function listParticipantsAllAvailable(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.judge_signature',
            'event_member_classes.participant_signature',
            'event_member_classes.team_name',
            'event_judge_member_assignments.order AS order',
            'cars.license_plate AS license_plate',
            'cars.vehicle AS vehicle',
            'cars.type AS type',
            'cars.vin_number AS vin_number',
            'event_member_classes.status_score',
            'event_member_classes.created_at',
            'users.name AS member_name',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_judges.id AS event_judge_id'
        )
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id')
                    ->orOn('event_judge_activities.competition_activity_id', '=', 'event_member_classes.competition_activity_id')
                    ->whereNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->orderBy('event_judge_member_assignments.order', 'asc')
            ->get();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $event_judge_member_assignment = new EventJudgeMemberAssignment();
            $status = $event_judge_member_assignment->getStatusAssignment($arr[$i]['event_judge_member_assignment_id']);
            $arr[$i]['status'] = $status;
        }

        return response()->json(['data' => $arr]);
    }

    public function listParticipantsAvailable(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.judge_signature',
            'event_member_classes.participant_signature',
            'event_member_classes.team_name',
            'event_judge_member_assignments.order AS order',
            'cars.license_plate AS license_plate',
            'cars.vehicle AS vehicle',
            'cars.type AS type',
            'cars.vin_number AS vin_number',
            'event_member_classes.status_score',
            'event_member_classes.created_at',
            'users.name AS member_name',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_judges.id AS event_judge_id'
        )
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id')
                    ->orOn('event_judge_activities.competition_activity_id', '=', 'event_member_classes.competition_activity_id')
                    ->whereNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('competition_activities.name', 'like', '%' . $request->search . '%')
                    ->orWhere('class_groups.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('event_judge_member_assignments.order', 'asc')
            ->get();

        $countEventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.created_at',
            'users.name AS member_name',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_judges.id AS event_judge_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id')
                    ->orOn('event_judge_activities.competition_activity_id', '=', 'event_member_classes.competition_activity_id')
                    ->whereNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('competition_activities.name', 'like', '%' . $request->search . '%')
                    ->orWhere('class_groups.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $event_judge_member_assignment = new EventJudgeMemberAssignment();
            $status = $event_judge_member_assignment->getStatusAssignment($arr[$i]['event_judge_member_assignment_id']);
            $arr[$i]['status'] = $status;
        }

        return response()->json(['data' => $arr, 'total' => $countEventMemberClass]);
    }

    public function listParticipantsAssignedToJudge(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'users.name AS member_name',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_judges.id AS event_judge_id',
            'events.event_country_id AS event_country_id',
            'class_countries.name AS event_country_name',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $countEventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'users.name AS member_name',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_judges.id AS event_judge_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $event_judge_member_assignment = new EventJudgeMemberAssignment();
            $status = $event_judge_member_assignment->getStatusAssignment($arr[$i]['event_judge_member_assignment_id']);
            $arr[$i]['status'] = $status;
        }

        return response()->json(['data' => $arr, 'total' => $countEventMemberClass]);
    }

    public function listParticipantsAssignedToJudgePerActivity(Request $request)
    {
    }

    public function listParticipantsAssignedToJudgePerActivityIncomplete(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'users.name AS member_name',
            'users.email AS member_email',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.order AS order',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_member_classes.grand_total AS event_member_classes_grand_total',
            'event_member_classes.form_assessment',
            'event_member_classes.judge_signature AS judge_signature',
            'event_member_classes.participant_signature AS participant_signature',
            'event_member_classes.team_name AS team_name',
            'cars.license_plate AS license_plate',
            'cars.vin_number AS vin_number',
            'cars.vehicle AS vehicle',
            'event_member_classes.created_at',
            'event_member_classes.status_score',
            'event_judges.id AS event_judge_id',
            'events.event_country_id AS event_country_id',
            'class_countries.name AS event_country_name',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name'
            // 'event_activity_class_forms.form_generator_id AS form_generator_id'
            // 'form_generators.title AS form_generator_title'
        )
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
            // ->leftJoin('event_activity_class_forms', function ($join) {
            //     $join->on('event_activity_class_forms.event_id', '=', 'event_judges.event_id')
            //         ->on('event_activity_class_forms.competition_activity_id', '=', 'competition_activities.id')
            //         ->on('event_activity_class_forms.class_grade_id', '=', 'class_grades.id');
            // })
            // ->leftJoin('form_generators', 'form_generators.id', '=', 'event_activity_class_forms.form_generator_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', $request->competition_activity_id)
            ->where('event_member_classes.form_assessment', '=', null)
            ->where('event_member_classes.grand_total', '=', null)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('event_judge_member_assignments.order', 'asc')
            ->get();

        $countEventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'users.name AS member_name',
            'users.email AS member_email',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_member_classes.grand_total AS event_member_classes_grand_total',
            'event_judges.id AS event_judge_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', $request->competition_activity_id)
            ->where('event_member_classes.form_assessment', '=', null)
            ->where('event_member_classes.grand_total', '=', null)
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $event_judge_member_assignment = new EventJudgeMemberAssignment();
            $status = $event_judge_member_assignment->getStatusAssignment($arr[$i]['event_judge_member_assignment_id']);
            $arr[$i]['status'] = $status;

            $eventActivityClassForms = EventActivityClassForm::select(
                // 'event_activity_class_forms.event_id',
                // 'event_activity_class_forms.competition_activity_id',
                // 'event_activity_class_forms.class_grade_id',
                'event_activity_class_forms.form_generator_id',
                // 'form_generators.id AS form_generator_id',
                'form_generators.title AS form_generator_title'
                // 'form_generators.form_assessment AS form_assessment'
            )
                ->leftJoin('form_generators', 'form_generators.id', '=', 'event_activity_class_forms.form_generator_id')
                ->where('event_id', $request->event_id)
                ->where('competition_activity_id', $request->competition_activity_id)
                ->where('class_grade_id', $arr[$i]['class_grade_id'])
                ->get();

            $arrEventActivityClassForms = [];

            foreach ($eventActivityClassForms as $object) {
                $arrEventActivityClassForms[] = $object->toArray();
            }

            $arr[$i]['forms'] = $arrEventActivityClassForms;
        }

        return response()->json(['data' => $arr, 'total' => $countEventMemberClass]);
    }

    public function listParticipantsAssignedToJudgePerActivityComplete(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.grand_total AS event_member_class_grand_total',
            'users.name AS member_name',
            'users.email AS member_email',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.order AS order',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_member_classes.form_id',
            'event_member_classes.form_title',
            'event_member_classes.form_assessment',
            'event_member_classes.judge_signature AS judge_signature',
            'event_member_classes.participant_signature AS participant_signature',
            'event_member_classes.team_name AS team_name',
            'cars.license_plate AS license_plate',
            'cars.vehicle AS vehicle',
            'cars.vin_number AS vin_number',
            'event_member_classes.grand_total',
            'event_member_classes.created_at',
            'event_member_classes.status_score',
            'event_judges.id AS event_judge_id',
            'events.event_country_id AS event_country_id',
            'class_countries.name AS event_country_name',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name'
        )
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', $request->competition_activity_id)
            ->whereNotNull('event_member_classes.form_assessment')
            ->whereNotNull('event_member_classes.grand_total')
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('event_judge_member_assignments.order', 'asc')
            ->get();

        $countEventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.grand_total AS event_member_class_grand_total',
            'users.name AS member_name',
            'users.email AS member_email',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_judges.id AS event_judge_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', $request->competition_activity_id)
            ->whereNotNull('event_member_classes.grand_total')
            ->whereNotNull('event_member_classes.form_assessment')
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $event_judge_member_assignment = new EventJudgeMemberAssignment();
            $status = $event_judge_member_assignment->getStatusAssignment($arr[$i]['event_judge_member_assignment_id']);
            $arr[$i]['status'] = $status;
        }

        return response()->json(['data' => $arr, 'total' => $countEventMemberClass]);
    }

    public function listParticipantsAssignedToJudgePerActivitySkipped(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.grand_total AS event_member_class_grand_total',
            'users.name AS member_name',
            'users.email AS member_email',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_judge_member_assignments.order AS order',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_member_classes.form_id',
            'event_member_classes.form_title',
            'event_member_classes.form_assessment',
            'event_member_classes.judge_signature AS judge_signature',
            'event_member_classes.participant_signature AS participant_signature',
            'event_member_classes.team_name AS team_name',
            'cars.license_plate AS license_plate',
            'cars.vin_number AS vin_number',
            'cars.vehicle AS vehicle',
            'event_member_classes.grand_total',
            'event_member_classes.created_at',
            'event_member_classes.status_score',
            'event_judges.id AS event_judge_id',
            'events.event_country_id AS event_country_id',
            'class_countries.name AS event_country_name',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name'
        )
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->leftJoin('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', $request->competition_activity_id)
            ->whereNotNull('event_member_classes.form_assessment')
            ->where('event_member_classes.grand_total', '=', null)
            ->orderBy('event_judge_member_assignments.order', 'asc')
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $countEventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.grand_total AS event_member_class_grand_total',
            'users.name AS member_name',
            'users.email AS member_email',
            'users.id AS member_id',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.event_id AS event_id',
            'event_judges.judge_id AS judge_id',
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judge_member_assignments.event_judge_activity_id AS event_judge_member_assignment_event_judge_activity_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_member_classes.form_id',
            'event_member_classes.form_title',
            'event_member_classes.form_assessment',
            'event_member_classes.grand_total',
            'event_judges.id AS event_judge_id',
            'events.event_country_id AS event_country_id',
            'class_countries.name AS event_country_name',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', function ($join) {
                $join->on('event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                    ->whereNotNull('event_judge_member_assignments.id');
            })
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->join('events', 'events.id', '=', 'event_judges.event_id')
            ->join('class_countries', 'class_countries.id', '=', 'events.event_country_id')
            ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', $request->competition_activity_id)
            ->whereNotNull('event_member_classes.form_assessment')
            ->where('event_member_classes.grand_total', '=', null)
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $event_judge_member_assignment = new EventJudgeMemberAssignment();
            $status = $event_judge_member_assignment->getStatusAssignment($arr[$i]['event_judge_member_assignment_id']);
            $arr[$i]['status'] = $status;
        }

        return response()->json(['data' => $arr, 'total' => $countEventMemberClass]);
    }

    public function listJudgesAssignedToParticipant(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_members.event_id AS event_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_member_classes.grand_total AS score',
            'event_member_classes.judge_signature AS judge_signature',
            'event_member_classes.participant_signature AS participant_signature',
            'event_member_classes.team_name AS team_name',
            'cars.license_plate AS license_plate',
            'cars.vehicle AS vehicle',
            'cars.type AS type',
            'cars.vin_number AS vin_number',
            'event_member_classes.status_score AS status_score',
            'event_member_classes.created_at AS created_at',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name'
        )
            ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->leftJoin('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->where('event_members.member_id', '=', $request->member_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->orderBy('event_member_classes.grand_total', 'asc')
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $countEventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'class_groups.name AS class_group_name',
            'class_groups.id AS class_group_id',
            'competition_activities.name AS competition_activity_name',
            'competition_activities.id AS competition_activity_id',
            'event_members.event_id AS event_id',
            'event_member_classes.competition_activity_id AS event_member_class_competition_activity_id',
            'event_member_classes.grand_total AS score',
            'class_grades.id AS class_grade_id',
            'class_grades.name AS class_grade_name'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->leftJoin('class_grades', 'class_grades.id', '=', 'event_member_classes.class_grade_id')
            ->where('event_members.member_id', '=', $request->member_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->orderBy('event_member_classes.grand_total', 'asc')
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $event_judge_member_assignment = new EventJudgeMemberAssignment();
            $judge = $event_judge_member_assignment->getJudgeAssignToParticipant($arr[$i]['event_member_class_id']);

            if (sizeof($judge) > 0) {
                $arr[$i]['judge'] = $judge[0];
            } else {
                $arr[$i]['judge'] = [];
            }
        }

        return response()->json(['data' => $arr, 'total' => $countEventMemberClass]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $member = User::select(
            'users.id',
            'users.grouped_user_id',
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.member_id', '=', 'users.id')
            ->leftJoin('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $judge = User::select(
            'users.id',
            'users.grouped_user_id',
            'event_judges.id AS event_judge_id',
            'event_judge_activities.id AS event_judge_activity_id'

        )
            ->leftJoin('event_judges', 'event_judges.judge_id', '=', 'users.id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->where('event_judge_activities.id', '=', $request->event_judge_activity_id)
            ->first();

        if ($member->grouped_user_id == $judge->grouped_user_id && $member->grouped_user_id != null && $judge->grouped_user_id != null) {
            return response()->json(['status' => 'failed', 'message' => 'cannot assign participant with the same grouped account'], $this->successStatus);
        }

        $count = EventJudgeMemberAssignment::select('id')
            ->where('event_member_class_id', '=', $request->event_member_class_id)
            ->where('event_judge_activity_id', '=', $request->event_judge_activity_id)
            ->count();

        $order = EventJudgeMemberAssignment::where('event_judge_activity_id', '=', $request->event_judge_activity_id)
            ->where('order', '=', $request->order)
            ->count();

        if ($order > 0) {
            return response()->json(['status' => 'failed', 'message' => 'Order already assigned'], $this->successStatus);
        }

        if ($count > 0) {
            return response()->json(['status' => 'failed', 'message' => 'participant already assigned with this judge'], $this->successStatus);
        } else {
            $input['event_member_class_id'] = $request->event_member_class_id;
            $input['event_judge_activity_id'] = $request->event_judge_activity_id;
            $input['order'] = $request->order;

            $save = EventJudgeMemberAssignment::create($input);

            if ($save) {
                return response()->json(['status' => 'success', 'message' => 'participant assign successfully'], $this->successStatus);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'failed assign participant'], $this->successStatus);
            }
        }
    }

    public function delete(Request $request)
    {
        $eventJudgeMemberAssignment = EventJudgeMemberAssignment::where('event_member_class_id', $request->event_member_class_id)
            ->where('event_judge_activity_id', $request->event_judge_activity_id)->first();

        if ($eventJudgeMemberAssignment) {
            $deleteEventJudgeMemberAssignment = EventJudgeMemberAssignment::where('event_member_class_id', $request->event_member_class_id)
                ->where('event_judge_activity_id', $request->event_judge_activity_id)->delete();

            if ($deleteEventJudgeMemberAssignment) {
                return response()->json(['status' => 'success', 'message' => 'assignment deleted successfully'], $this->successStatus);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'failed delete assignment'], $this->successStatus);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'assignment not found'], $this->successStatus);
        }
    }
}
