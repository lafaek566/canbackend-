<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CanTuneBracket;
use App\EventJudgeActivity;
use App\EventJudgeMemberAssignment;
use function GuzzleHttp\json_decode;
use App\CanTuneConsumerPyramid;
use App\CanTuneProsumerPyramid;
use App\CanTuneProfessionalPyramid;
use App\EventMemberClass;

class CanTuneScoreController extends Controller
{
    public $successStatus = 200;

    public function checkConsumerPyramid(Request $request)
    {
        $countCanTuneConsumerPyramid = CanTuneConsumerPyramid::select(
            'id'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        if ($countCanTuneConsumerPyramid > 0) {
            return response()->json(['status' => 'success', 'exist' => true], 200);
        } else {
            return response()->json(['status' => 'success', 'exist' => false], 200);
        }
    }

    public function checkProsumerPyramid(Request $request)
    {
        $countCanTuneProsumerPyramid = CanTuneProsumerPyramid::select(
            'id'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        if ($countCanTuneProsumerPyramid > 0) {
            return response()->json(['status' => 'success', 'exist' => true], 200);
        } else {
            return response()->json(['status' => 'success', 'exist' => false], 200);
        }
    }

    public function checkProfessionalPyramid(Request $request)
    {
        $countCanTuneProfessionalPyramid = CanTuneProfessionalPyramid::select(
            'id'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_professional_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        if ($countCanTuneProfessionalPyramid > 0) {
            return response()->json(['status' => 'success', 'exist' => true], 200);
        } else {
            return response()->json(['status' => 'success', 'exist' => false], 200);
        }
    }

    public function getConsumerWinner(Request $request)
    {
        $countCanTuneConsumerPyramid = CanTuneConsumerPyramid::select(
            'can_tune_consumer_pyramids.id AS can_tune_consumer_pyramid_id'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        $arrWinner = [];

        if ($countCanTuneConsumerPyramid < 2) {
            return response()->json(['status' => 'success', 'data' => $arrWinner], 200);
        } else if ($countCanTuneConsumerPyramid >= 2 && $countCanTuneConsumerPyramid <= 6) {
            $canTuneConsumerWinner = CanTuneConsumerPyramid::select(
                'can_tune_consumer_pyramids.id AS can_tune_consumer_pyramid_id',
                'event_member_class_id',
                'tonal_low',
                'tonal_mid_bass',
                'tonal_mid_low',
                'tonal_mid_high',
                'tonal_high',
                'tonal_total',
                'deduction_point',
                'deduction_comment',
                'total',
                'can_tune_bracket_id',
                'time_start',
                'time_end',
                'event_members.member_id AS member_id',
                'users.name AS member_name',
                'user_profiles.avatar AS member_avatar'
            )
                ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->join('users', 'users.id', '=', 'event_members.member_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->orderBy('total', 'desc')
                ->orderBy('users.name', 'asc')
                ->limit(1)
                ->get();

            $arr = [];

            foreach ($canTuneConsumerWinner as $object) {
                $arr[] = $object->toArray();
            }


            return response()->json(['data' => $arr], 200);
        } else {

            $arr = [];

            for ($i = 1; $i < 7; $i++) {
                $canTuneConsumerWinnerBracket = CanTuneConsumerPyramid::select(
                    'can_tune_consumer_pyramids.id AS can_tune_consumer_pyramid_id',
                    'event_member_class_id',
                    'tonal_low',
                    'tonal_mid_bass',
                    'tonal_mid_low',
                    'tonal_mid_high',
                    'tonal_high',
                    'tonal_total',
                    'deduction_point',
                    'deduction_comment',
                    'total',
                    'can_tune_bracket_id',
                    'time_start',
                    'time_end',
                    'event_members.member_id AS member_id',
                    'users.name AS member_name',
                    'user_profiles.avatar AS member_avatar'
                )
                    ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
                    ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                    ->join('users', 'users.id', '=', 'event_members.member_id')
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                    ->where('event_members.event_id', '=', $request->event_id)
                    ->where('can_tune_bracket_id', '=', $i)
                    ->orderBy('total', 'desc')
                    ->orderBy('users.name', 'asc')
                    ->limit(2)
                    ->get();

                $count = $canTuneConsumerWinnerBracket->count();

                if ($count > 0) {
                    foreach ($canTuneConsumerWinnerBracket as $object) {
                        $arr[] = $object->toArray();
                    }
                }
            }

            $countAssessment = CanTuneConsumerPyramid::select(
                'can_tune_consumer_pyramids.id AS can_tune_consumer_pyramid_id'
            )
                ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->join('users', 'users.id', '=', 'event_members.member_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('can_tune_consumer_pyramids.status_assessment', '=', 0)
                ->count();

            if ($countAssessment > 0) {
                $status_assessment = false;
            } else {
                $status_assessment = true;
            }

            for ($i = 0; $i < sizeof($arr); $i++) {
                if ($arr[$i]['can_tune_bracket_id'] === 1 || $arr[$i]['can_tune_bracket_id'] === 2 || $arr[$i]['can_tune_bracket_id'] === 3) {
                    $arr[$i]['prosumer_bracket_id'] = 7;
                } else {
                    $arr[$i]['prosumer_bracket_id'] = 8;
                }
            }

            return response()->json(['data' => $arr, 'status_assessment' => $status_assessment], 200);
        }
    }

    public function getProsumerWinner(Request $request)
    {
        $countCanTuneProsumerPyramid = CanTuneProsumerPyramid::select(
            'can_tune_prosumer_pyramids.id AS can_tune_consumer_pyramid_id'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        $arrWinner = [];

        if ($countCanTuneProsumerPyramid < 7) {
            return response()->json(['status' => 'success', 'data' => $arrWinner], 200);
        } else if ($countCanTuneProsumerPyramid >= 7 && $countCanTuneProsumerPyramid <= 18) {
            $canTuneProsumerWinner = CanTuneProsumerPyramid::select(
                'can_tune_prosumer_pyramids.id AS can_tune_consumer_pyramid_id',
                'event_member_class_id',
                'tonal_low',
                'tonal_mid_bass',
                'tonal_mid_low',
                'tonal_mid_high',
                'tonal_high',
                'tonal_total',
                'deduction_point',
                'deduction_comment',
                'total',
                'can_tune_bracket_id',
                'time_start',
                'time_end',
                'event_members.member_id AS member_id',
                'users.name AS member_name',
                'user_profiles.avatar AS member_avatar'
            )
                ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->join('users', 'users.id', '=', 'event_members.member_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->orderBy('total', 'desc')
                ->orderBy('users.name', 'asc')
                ->limit(1)
                ->get();

            $arr = [];

            foreach ($canTuneProsumerWinner as $object) {
                $arr[] = $object->toArray();
            }


            return response()->json(['data' => $arr], 200);
        } else {

            $arr = [];

            for ($i = 1; $i < 3; $i++) {
                $canTuneProsumerWinnerBracket = CanTuneProsumerPyramid::select(
                    'can_tune_prosumer_pyramids.id AS can_tune_prosumer_pyramid_id',
                    'event_member_class_id',
                    'tonal_low',
                    'tonal_mid_bass',
                    'tonal_mid_low',
                    'tonal_mid_high',
                    'tonal_high',
                    'tonal_total',
                    'deduction_point',
                    'deduction_comment',
                    'total',
                    'can_tune_bracket_id',
                    'time_start',
                    'time_end',
                    'event_members.member_id AS member_id',
                    'users.name AS member_name',
                    'user_profiles.avatar AS member_avatar'
                )
                    ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
                    ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                    ->join('users', 'users.id', '=', 'event_members.member_id')
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                    ->where('event_members.event_id', '=', $request->event_id)
                    ->where('can_tune_bracket_id', '=', $i)
                    ->orderBy('total', 'desc')
                    ->orderBy('users.name', 'asc')
                    ->limit(2)
                    ->get();

                $count = $canTuneProsumerWinnerBracket->count();

                if ($count > 0) {
                    foreach ($canTuneProsumerWinnerBracket as $object) {
                        $arr[] = $object->toArray();
                    }
                }
            }

            $countAssessment = CanTuneProsumerPyramid::select(
                'can_tune_prosumer_pyramids.id AS can_tune_prosumer_pyramid_id'
            )
                ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->join('users', 'users.id', '=', 'event_members.member_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('can_tune_prosumer_pyramids.status_assessment', '=', 0)
                ->count();

            if ($countAssessment > 0) {
                $status_assessment = false;
            } else {
                $status_assessment = true;
            }

            for ($i = 0; $i < sizeof($arr); $i++) {
                $arr[$i]['professional_bracket_id'] = 9;
            }

            return response()->json(['data' => $arr, 'status_assessment' => $status_assessment], 200);
        }
    }

    public function getProfessionalWinner(Request $request)
    {
        $countCanTuneProfessionalPyramid = CanTuneProfessionalPyramid::select(
            'can_tune_professional_pyramids.id AS can_tune_consumer_pyramid_id'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_professional_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        $arrWinner = [];

        if ($countCanTuneProfessionalPyramid < 19) {
            return response()->json(['status' => 'success', 'data' => $arrWinner], 200);
        } else {
            $canTuneProfessionalWinner = CanTuneProfessionalPyramid::select(
                'can_tune_professional_pyramids.id AS can_tune_professional_pyramid_id',
                'event_member_class_id',
                'tonal_low',
                'tonal_mid_bass',
                'tonal_mid_low',
                'tonal_mid_high',
                'tonal_high',
                'tonal_total',
                'staging_left',
                'staging_right',
                'height_left',
                'height_lfctr',
                'height_center',
                'height_rhctr',
                'height_right',
                'height_total',
                'distance_left',
                'distance_lfctr',
                'distance_center',
                'distance_rhctr',
                'distance_right',
                'distance_total',
                'depth_c1_to_c2',
                'depth_c2_to_c3',
                'depth_total',
                'staging_total',
                'deduction_point',
                'deduction_comment',
                'grand_total',
                'can_tune_bracket_id',
                'time_start',
                'time_end',
                'event_members.member_id AS member_id',
                'users.name AS member_name',
                'user_profiles.avatar AS member_avatar'
            )
                ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->join('users', 'users.id', '=', 'event_members.member_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->orderBy('total', 'desc')
                ->orderBy('users.name', 'asc')
                ->limit(1)
                ->get();

            $arr = [];

            foreach ($canTuneProfessionalWinner as $object) {
                $arr[] = $object->toArray();
            }


            return response()->json(['data' => $arr], 200);
        } 
    }

    public function getBracket(Request $request)
    {
        $canTuneBracket = CanTuneBracket::select(
            'id',
            'name',
            'class_grade_id'
        )
            ->where('class_grade_id', '=', $request->class_grade_id)
            ->get();

        $arr = [];

        foreach ($canTuneBracket as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr], 200);
    }

    public function getJudgeCanTuneEvent(Request $request)
    {
        $eventJudgeActivity = EventJudgeActivity::select(
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judges.judge_id AS judge_id',
            'users.name AS judge_name',
            'user_profiles.avatar AS judge_avatar'
        )
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_judges.judge_id')
            ->where('event_judge_activities.competition_activity_id', '=', 5)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventJudgeActivityCount = EventJudgeActivity::select(
            'event_judge_activities.id AS event_judge_activity_id',
            'event_judges.judge_id AS judge_id',
            'users.name AS judge_name'
        )
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->join('users', 'users.id', '=', 'event_judges.judge_id')
            ->where('event_judge_activities.competition_activity_id', '=', 5)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->count();

        $arr = [];

        foreach ($eventJudgeActivity as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeActivityCount], 200);
    }

    public function getParticipantRegistered(Request $request)
    {
        $eventJudgeMemberAssignment = EventJudgeMemberAssignment::select(
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.id AS event_member_id',
            'event_member_classes.id AS event_member_class_id',
            'users.id AS member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventJudgeMemberAssignmentCount = EventJudgeMemberAssignment::select(
            'event_judge_member_assignments.id AS event_judge_member_assignment_id',
            'event_members.id AS event_member_id',
            'event_member_classes.id AS event_member_class_id',
            'users.id AS member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->where('event_members.event_id', '=', $request->event_id)
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->count();

        $arr = [];

        foreach ($eventJudgeMemberAssignment as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $arr[$i]['can_tune_bracket_id'] = null;
        }

        return response()->json(['data' => $arr, 'total' => $eventJudgeMemberAssignmentCount], 200);
    }

    public function getConsumerList(Request $request)
    {
        $canTuneConsumerPyramid = CanTuneConsumerPyramid::select(
            'can_tune_consumer_pyramids.event_member_class_id AS event_member_class_id',
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
            'can_tune_consumer_pyramids.can_tune_bracket_id AS can_tune_bracket_id',
            'can_tune_brackets.name AS can_tune_bracket_name',
            'time_start',
            'time_end',
            'status_assessment',
            'status_submit_prosumer'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_consumer_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $canTuneConsumerPyramidCount = CanTuneConsumerPyramid::select(
            'can_tune_consumer_pyramids.event_member_class_id AS event_member_class_id',
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
            'can_tune_consumer_pyramids.can_tune_bracket_id AS can_tune_bracket_id',
            'can_tune_brackets.name AS can_tune_bracket_name',
            'time_start',
            'time_end',
            'status_assessment',
            'status_submit_prosumer'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_consumer_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 1)
            ->where('event_members.event_id', '=', $request->event_id)
            ->count();

        $arr = [];

        foreach ($canTuneConsumerPyramid as $object) {
            $arr[] = $object->toArray();
        }

        $model = new CanTuneConsumerPyramid();
        $statusAllAssessment = $model->getStatusAssessmentAllConsumer($request->event_id);
        $statusAllSubmit = $model->getStatusSubmitAllConsumer($request->event_id);
        $arr['status_all_assessment'] = $statusAllAssessment;
        $arr['status_all_submit_prosumer'] = $statusAllSubmit;

        return response()->json(['data' => $arr, 'total' => $canTuneConsumerPyramidCount], 200);
    }

    public function getProsumerList(Request $request)
    {
        $canTuneProsumerPyramid = CanTuneProsumerPyramid::select(
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
            'status_submit_prosumer'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_prosumer_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 2)
            ->where('event_members.event_id', '=', $request->event_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

            $canTuneProsumerPyramidCount = CanTuneProsumerPyramid::select(
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
                'status_submit_prosumer'
            )
                ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->join('users', 'users.id', '=', 'event_members.member_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_prosumer_pyramids.can_tune_bracket_id')
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 2)
                ->where('event_members.event_id', '=', $request->event_id)
                ->count();

        $arr = [];

        foreach ($canTuneProsumerPyramid as $object) {
            $arr[] = $object->toArray();
        }

        $model = new CanTuneProsumerPyramid();
        $statusAllAssessment = $model->getStatusAssessmentAllProsumer($request->event_id);
        $statusAllSubmit = $model->getStatusSubmitAllProsumer($request->event_id);
        $arr['status_all_assessment'] = $statusAllAssessment;
        $arr['status_all_submit_prosumer'] = $statusAllSubmit;

        return response()->json(['data' => $arr, 'total' => $canTuneProsumerPyramidCount], 200);
    }

    public function getProfessionalList(Request $request)
    {
        $canTuneProfessionalPyramid = CanTuneProfessionalPyramid::select(
            'can_tune_professional_pyramids.event_member_class_id AS event_member_class_id',
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
            'can_tune_professional_pyramids.can_tune_bracket_id AS can_tune_bracket_id',
            'can_tune_brackets.name AS can_tune_bracket_name',
            'time_start',
            'time_end',
            'status_assessment',
            'status_submit_final'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_professional_pyramids.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('users', 'users.id', '=', 'event_members.member_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_professional_pyramids.can_tune_bracket_id')
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('event_member_classes.class_grade_id', '=', 3)
            ->where('event_members.event_id', '=', $request->event_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

            $canTuneProfessionalPyramidCount = CanTuneProfessionalPyramid::select(
                'can_tune_professional_pyramids.event_member_class_id AS event_member_class_id',
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
                'can_tune_professional_pyramids.can_tune_bracket_id AS can_tune_bracket_id',
                'can_tune_brackets.name AS can_tune_bracket_name',
                'time_start',
                'time_end',
                'status_assessment',
                'status_submit_final'
            )
                ->join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_professional_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->join('users', 'users.id', '=', 'event_members.member_id')
                ->join('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->join('can_tune_brackets', 'can_tune_brackets.id', '=', 'can_tune_professional_pyramids.can_tune_bracket_id')
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 3)
                ->where('event_members.event_id', '=', $request->event_id)
                ->count();

        $arr = [];

        foreach ($canTuneProfessionalPyramid as $object) {
            $arr[] = $object->toArray();
        }

        $model = new CanTuneProfessionalPyramid();
        $statusAllAssessment = $model->getStatusAssessmentAllProfessional($request->event_id);
        $statusAllSubmit = $model->getStatusSubmitAllprofessional($request->event_id);
        $arr['status_all_assessment'] = $statusAllAssessment;
        $arr['status_all_submit_professional'] = $statusAllSubmit;

        return response()->json(['data' => $arr, 'total' => $canTuneProfessionalPyramidCount], 200);
    }

    public function consumerSubmitProsumer(Request $request)
    {
        $model = new CanTuneConsumerPyramid();
        $statusAllAssessment = $model->getStatusAssessmentAllConsumer($request->event_id);

        if ($statusAllAssessment === false) {
            return response()->json(['status' => 'failed', 'message' => 'all consumer participant must be assessed before submit to prosumer'], $this->successStatus);
        } else {
            $eventMemberClass = EventMemberClass::select(
                'event_member_classes.id AS id'
            )
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 1)
                ->get();


            $update = CanTuneConsumerPyramid::join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_consumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 1)
                ->update(
                    [
                        'status_submit_prosumer' => 1
                    ]
                );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'consumer submitted to prosumer successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'failed submit consumer to prosumer'], 200);
            }
        }
    }

    public function prosumerSubmitProfessional(Request $request)
    {
        $model = new CanTuneProsumerPyramid();
        $statusAllAssessment = $model->getStatusAssessmentAllProsumer($request->event_id);

        if ($statusAllAssessment === false) {
            return response()->json(['status' => 'failed', 'message' => 'all consumer participant must be assessed before submit to professional'], $this->successStatus);
        } else {
            $eventMemberClass = EventMemberClass::select(
                'event_member_classes.id AS id'
            )
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 2)
                ->get();


            $update = CanTuneProsumerPyramid::join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_prosumer_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 2)
                ->update(
                    [
                        'status_submit_prosumer' => 1
                    ]
                );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'prosumer submitted to professional successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'failed submit prosumer to professional'], 200);
            }
        }
    }

    public function professionalSubmitFinal(Request $request)
    {
        $model = new CanTuneProfessionalPyramid();
        $statusAllAssessment = $model->getStatusAssessmentAllProfessional($request->event_id);

        if ($statusAllAssessment === false) {
            return response()->json(['status' => 'failed', 'message' => 'all consumer participant must be assessed before submit to professional'], $this->successStatus);
        } else {
            $eventMemberClass = EventMemberClass::select(
                'event_member_classes.id AS id'
            )
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 3)
                ->get();


            $update = CanTuneProfessionalPyramid::join('event_member_classes', 'event_member_classes.id', '=', 'can_tune_professional_pyramids.event_member_class_id')
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->where('event_members.event_id', '=', $request->event_id)
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 2)
                ->update(
                    [
                        'status_submit_final' => 1
                    ]
                );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'professional have been submited successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'failed submit professional'], 200);
            }
        }
    }

    public function storeConsumer(Request $request)
    {
        $arrParticipant = json_decode($request->participant_data, true);

        for ($i = 0; $i < sizeof($arrParticipant); $i++) {
            $input['event_member_class_id'] = $arrParticipant[$i]['event_member_class_id'];
            $input['can_tune_bracket_id'] = $arrParticipant[$i]['can_tune_bracket_id'];

            $saveScore = CanTuneConsumerPyramid::create($input);
        }

        return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
    }

    public function storeProsumer(Request $request)
    {
        $arrConsumerWinnerData = json_decode($request->consumer_winner_data, true);

        for ($i = 0; $i < sizeof($arrConsumerWinnerData); $i++) {
            $event_member_class_id = (int) $arrConsumerWinnerData[$i]['event_member_class_id'];
            $judge_id = (int) $arrConsumerWinnerData[$i]['judge_id'];
            $consumer_bracket_id = (int) $arrConsumerWinnerData[$i]['consumer_bracket_id'];
            $event_id = (int) $request->event_id;

            $eventMemberClass = EventMemberClass::where('id', '=', $event_member_class_id)->first();

            if ($eventMemberClass) {
                $input['event_member_id'] = $eventMemberClass->event_member_id;
                $input['competition_activity_id'] = 5;
                $input['class_group_id'] = 91;
                $input['class_grade_id'] = 2;
                $input['car_id'] = $eventMemberClass->car_id;
                $input['team_name'] = $eventMemberClass->team_name;

                $saveClass = EventMemberClass::create($input);

                if ($saveClass) {
                    $eventJudgeActivities = EventJudgeActivity::select('event_judge_activities.id AS id')->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->where('event_judges.event_id', '=', $event_id)->where('event_judges.judge_id', '=', $judge_id)
                        ->where('event_judge_activities.competition_activity_id', '=', 5)->first();

                    $eventMemberClass = EventMemberClass::select('event_member_classes.id AS id')->where('event_member_id', $eventMemberClass->event_member_id)->where('competition_activity_id', 5)
                        ->where('class_group_id', 91)->where('class_grade_id', 2)->where('car_id', $request->car_id)->where('team_name', $request->team_name)->first();

                    $inputAssign['event_member_class_id'] = $eventMemberClass->id;
                    $inputAssign['event_judge_activity_id'] = $eventJudgeActivities->id;

                    $saveAssign = EventJudgeMemberAssignment::create($inputAssign);

                    if ($saveAssign) {
                        if ($consumer_bracket_id === 1 || $consumer_bracket_id === 2 || $consumer_bracket_id === 3) {
                            $prosumer_bracket_id = 7;
                        } else {
                            $prosumer_bracket_id = 8;
                        }

                        $inputProsumer['event_member_class_id'] = $eventMemberClass->id;
                        $inputProsumer['can_tune_bracket_id'] = $prosumer_bracket_id;

                        $saveProsumer = CanTuneProsumerPyramid::create($inputProsumer);
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'failed assign participant'], 200);
                    }
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'failed create class of participant'], 200);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'event member class not found'], 200);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'CAN Tune Prosumer created successfully'], 200);
    }

    public function storeProfessional(Request $request)
    {
        $arrProsumer_winner_data = json_decode($request->prosumer_winner_data, true);

        for ($i = 0; $i < sizeof($arrProsumer_winner_data); $i++) {
            $event_member_class_id = (int) $arrProsumer_winner_data[$i]['event_member_class_id'];
            $judge_id = (int) $arrProsumer_winner_data[$i]['judge_id'];
            $prosumer_bracket_id = (int) $arrProsumer_winner_data[$i]['prosumer_bracket_id'];
            $event_id = (int) $request->event_id;

            $eventMemberClass = EventMemberClass::where('id', '=', $event_member_class_id)->first();

            if ($eventMemberClass) {
                $input['event_member_id'] = $eventMemberClass->event_member_id;
                $input['competition_activity_id'] = 5;
                $input['class_group_id'] = 92;
                $input['class_grade_id'] = 3;
                $input['car_id'] = $eventMemberClass->car_id;
                $input['team_name'] = $eventMemberClass->team_name;

                $saveClass = EventMemberClass::create($input);

                if ($saveClass) {
                    $eventJudgeActivities = EventJudgeActivity::select('event_judge_activities.id AS id')->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->where('event_judges.event_id', '=', $event_id)->where('event_judges.judge_id', '=', $judge_id)
                        ->where('event_judge_activities.competition_activity_id', '=', 5)->first();

                    $eventMemberClass = EventMemberClass::select('event_member_classes.id AS id')->where('event_member_id', $eventMemberClass->event_member_id)->where('competition_activity_id', 5)
                        ->where('class_group_id', 92)->where('class_grade_id', 3)->where('car_id', $request->car_id)->where('team_name', $request->team_name)->first();

                    $inputAssign['event_member_class_id'] = $eventMemberClass->id;
                    $inputAssign['event_judge_activity_id'] = $eventJudgeActivities->id;

                    $saveAssign = EventJudgeMemberAssignment::create($inputAssign);

                    if ($saveAssign) {

                        $professional_bracket_id = 9;

                        $inputProfessional['event_member_class_id'] = $eventMemberClass->id;
                        $inputProfessional['can_tune_bracket_id'] = $professional_bracket_id;

                        $saveProfessional = CanTuneProfessionalPyramid::create($inputProfessional);
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'failed assign participant'], 200);
                    }
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'failed create class of participant'], 200);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'event member class not found'], 200);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'CAN Tune Professional created successfully'], 200);
    }

    public function updateConsumer(Request $request)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanTuneConsumerPyramid = CanTuneConsumerPyramid::select('id')->where('id', '=', $request->id)->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanTuneConsumerPyramid > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $date = date('Y-m-d H:i:s');

                    $input['tonal_low'] = (float) $request->tonal_low;
                    $input['tonal_mid_bass'] = (float) $request->tonal_mid_bass;
                    $input['tonal_mid_low'] = (float) $request->tonal_mid_low;
                    $input['tonal_mid_high'] = (float) $request->tonal_mid_high;
                    $input['tonal_high'] = (float) $request->tonal_high;
                    $input['deduction_point'] = (float) $request->deduction_point;
                    $input['deduction_comment'] = $request->deduction_comment;
                    $input['time_start'] = $request->time_start;
                    $input['time_end'] = $date;
                    $input['status_assessment'] = 1;

                    $input['total'] = $request->total;
                    $input['tonal_total'] = $request->tonal_total;


                    $update = CanTuneConsumerPyramid::where('id', $request->id)->update(
                        [
                            'tonal_low' => $input['tonal_low'],
                            'tonal_mid_bass' => $input['tonal_mid_bass'],
                            'tonal_mid_low' => $input['tonal_mid_low'],
                            'tonal_mid_high' => $input['tonal_mid_high'],
                            'deduction_point' => $input['deduction_point'],
                            'deduction_comment' => $input['deduction_comment'],
                            'total' => $input['total'],
                            'tonal_total' => $input['tonal_total'],
                            'time_start' => $input['time_start'],
                            'time_end' => $input['time_end'],
                            'status_assessment' => $input['status_assessment']
                        ]
                    );

                    if ($update) {
                        return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
                    }
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'assessment not found in this class'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function updateProsumer(Request $request)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanTuneConsumerPyramid = CanTuneProsumerPyramid::select('id')->where('id', '=', $request->id)->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanTuneConsumerPyramid > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $date = date('Y-m-d H:i:s');

                    $input['tonal_low'] = (float) $request->tonal_low;
                    $input['tonal_mid_bass'] = (float) $request->tonal_mid_bass;
                    $input['tonal_mid_low'] = (float) $request->tonal_mid_low;
                    $input['tonal_mid_high'] = (float) $request->tonal_mid_high;
                    $input['tonal_high'] = (float) $request->tonal_high;
                    $input['deduction_point'] = (float) $request->deduction_point;
                    $input['deduction_comment'] = $request->deduction_comment;
                    $input['time_start'] = $request->time_start;
                    $input['time_end'] = $date;
                    $input['status_assessment'] = 1;

                    $input['total'] = $request->total;
                    $input['tonal_total'] = $request->tonal_total;

                    $update = CanTuneProsumerPyramid::where('id', $request->id)->update(
                        [
                            'tonal_low' => $input['tonal_low'],
                            'tonal_mid_bass' => $input['tonal_mid_bass'],
                            'tonal_mid_low' => $input['tonal_mid_low'],
                            'tonal_mid_high' => $input['tonal_mid_high'],
                            'deduction_point' => $input['deduction_point'],
                            'deduction_comment' => $input['deduction_comment'],
                            'total' => $input['total'],
                            'tonal_total' => $input['tonal_total'],
                            'time_start' => $input['time_start'],
                            'time_end' => $input['time_end'],
                            'status_assessment' => $input['status_assessment']
                        ]
                    );

                    if ($update) {
                        return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
                    }
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'assessment not found in this class'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function updateProfessional(Request $request)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanTuneProfessionalPyramid = CanTuneProfessionalPyramid::select('id')->where('id', '=', $request->id)->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanTuneProfessionalPyramid > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $date = date('Y-m-d H:i:s');

                    $input['tonal_low'] = (float) $request->tonal_low;
                    $input['tonal_mid_bass'] = (float) $request->tonal_mid_bass;
                    $input['tonal_mid_low'] = (float) $request->tonal_mid_low;
                    $input['tonal_mid_high'] = (float) $request->tonal_mid_high;
                    $input['tonal_high'] = (float) $request->tonal_high;
                    $input['staging_left'] = (float) $request->staging_left;
                    $input['staging_right'] = (float) $request->staging_right;
                    $input['height_left'] = (float) $request->height_left;
                    $input['height_lfctr'] = (float) $request->height_lfctr;
                    $input['height_center'] = (float) $request->height_center;
                    $input['height_rhctr'] = (float) $request->height_rhctr;
                    $input['height_right'] = (float) $request->height_right;
                    $input['height_total'] = (float) $request->height_total;
                    $input['distance_left'] = (float) $request->distance_left;
                    $input['distance_lfctr'] = (float) $request->distance_lfctr;
                    $input['distance_center'] = (float) $request->distance_center;
                    $input['distance_rhctr'] = (float) $request->distance_rhctr;
                    $input['distance_right'] = (float) $request->distance_right;
                    $input['distance_total'] = (float) $request->distance_total;
                    $input['depth_c1_to_c2'] = (float) $request->depth_c1_to_c2;
                    $input['depth_c2_to_c3'] = (float) $request->depth_c2_to_c3;
                    $input['depth_total'] = (float) $request->depth_total;
                    $input['staging_total'] = (float) $request->staging_total;
                    $input['deduction_point'] = $request->deduction_point;
                    $input['deduction_comment'] = $request->deduction_comment;
                    $input['grand_total'] = $request->grand_total;
                    $input['time_start'] = $request->time_start;
                    $input['time_end'] = $date;
                    $input['status_assessment'] = 1;


                    $update = CanTuneProsumerPyramid::where('id', $request->id)->update(
                        [
                            'tonal_low' => $input['tonal_low'],
                            'tonal_mid_bass' => $input['tonal_mid_bass'],
                            'tonal_mid_low' => $input['tonal_mid_low'],
                            'tonal_mid_high' => $input['tonal_mid_high'],
                            'tonal_high' => $input['tonal_high'],
                            'tonal_total' => $input['tonal_total'],
                            'staging_left' => $input['staging_left'],
                            'staging_right' => $input['staging_right'],
                            'height_left' => $input['height_left'],
                            'height_lfctr' => $input['height_lfctr'],
                            'height_center' => $input['height_center'],
                            'height_rhctr' => $input['height_rhctr'],
                            'height_right' => $input['height_right'],
                            'height_total' => $input['height_total'],
                            'distance_left' => $input['distance_left'],
                            'distance_lfctr' => $input['distance_lfctr'],
                            'distance_center' => $input['distance_center'],
                            'distance_rhctr' => $input['distance_rhctr'],
                            'distance_right' => $input['distance_right'],
                            'distance_total' => $input['distance_total'],
                            'depth_c1_to_c2' => $input['depth_c1_to_c2'],
                            'depth_c2_to_c3' => $input['depth_c2_to_c3'],
                            'depth_total' => $input['depth_total'],
                            'staging_total' => $input['staging_total'],
                            'deduction_point' => $input['deduction_point'],
                            'deduction_comment' => $input['deduction_comment'],
                            'grand_total' => $input['grand_total'],
                            'time_start' => $input['time_start'],
                            'time_end' => $input['time_end'],
                            'status_assessment' => $input['status_assessment']
                        ]
                    );

                    if ($update) {
                        return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
                    }
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'assessment not found in this class'], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }
}
