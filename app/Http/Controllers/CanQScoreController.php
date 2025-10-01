<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Event;
use App\CanQScore;
use App\CanQImagingPositionAndFocus;
use App\CanQListeningPleasure;
use App\CanQSpectralBalanceAndLinearity;
use App\CanQStaging;
use App\CanQTonalAccuracy;

class CanQScoreController extends Controller
{
    public $successStatus = 200;

    public function listAllParticipantAssessed(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventMemberClassCount = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberClassCount]);
    }

    public function listAllParticipantNotAssessed(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventMemberClassCount = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberClassCount]);
    }

    public function listParticipantOfJudgeAssessed(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventMemberClassCount = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberClassCount]);
    }

    public function listParticipantOfJudgeNotAssessed(Request $request)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $eventMemberClassCount = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'event_member_classes.event_member_id AS event_member_id',
            'users.name AS member_name',
            'user_profiles.avatar AS member_avatar',
            'class_groups.name AS class_group_name',
            'can_q_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_q_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 1)
            ->where('event_judges.event_id', '=', $request->event_id)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where('event_members.event_id', '=', $request->event_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberClassCount]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_member_class_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQScore = CanQScore::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanQScore > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['vision_block'] = (float) $request->vision_block;
                $input['seating_position'] = (float) $request->seating_position;
                $input['noise_floor'] = (float) $request->noise_floor;
                $input['alternator_whine'] = (float) $request->alternator_whine;
                $input['coming_late'] = (float) $request->coming_late;
                $input['system_down'] = (float) $request->system_down;
                $input['system_volume_level_suggested_one'] = (float) $request->system_volume_level_suggested_one;
                $input['system_volume_level_suggested_two'] = (float) $request->system_volume_level_suggested_two;
                $input['system_volume_level_suggested_three'] = (float) $request->system_volume_level_suggested_three;
                $input['system_volume_level_suggested_use'] = (float) $request->system_volume_level_suggested_use;
                $input['cheating_action'] = (float) $request->cheating_action;
                $input['deduction_point'] = (float) $request->deduction_point;
                $input['cheating_comment'] = $request->cheating_comment;
                $input['deduction_comment'] = $request->deduction_comment;
                $input['time_start'] = $request->time_start;
                $input['grand_total'] = (float) $request->grand_total;

                $saveScore = CanQScore::create($input);

                if ($saveScore) {
                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function storeImagingPositionAndFocus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_member_class_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQImagingPositionAndFocus = CanQImagingPositionAndFocus::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanQImagingPositionAndFocus > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['left_drum'] = (float) $request->left_drum;
                $input['left_guitar'] = (float) $request->left_guitar;
                $input['left_piano'] = (float) $request->left_piano;
                $input['left_vibraphone'] = (float) $request->left_vibraphone;
                $input['left_trumpet'] = (float) $request->left_trumpet;
                $input['left_total'] = (float) $request->left_total;
                $input['lfctr_drum'] = (float) $request->lfctr_drum;
                $input['lfctr_guitar'] = (float) $request->lfctr_guitar;
                $input['lfctr_piano'] = (float) $request->lfctr_piano;
                $input['lfctr_vibraphone'] = (float) $request->lfctr_vibraphone;
                $input['lfctr_trumpet'] = (float) $request->lfctr_trumpet;
                $input['lfctr_total'] = (float) $request->lfctr_total;
                $input['center_drum'] = (float) $request->center_drum;
                $input['center_guitar'] = (float) $request->center_guitar;
                $input['center_piano'] = (float) $request->center_piano;
                $input['center_vibraphone'] = (float) $request->center_vibraphone;
                $input['center_trumpet'] = (float) $request->center_trumpet;
                $input['center_total'] = (float) $request->center_total;
                $input['rhctr_drum'] = (float) $request->rhctr_drum;
                $input['rhctr_guitar'] = (float) $request->rhctr_guitar;
                $input['rhctr_piano'] = (float) $request->rhctr_piano;
                $input['rhctr_vibraphone'] = (float) $request->rhctr_vibraphone;
                $input['rhctr_trumpet'] = (float) $request->rhctr_trumpet;
                $input['rhctr_total'] = (float) $request->rhctr_total;
                $input['right_drum'] = (float) $request->right_drum;
                $input['right_guitar'] = (float) $request->right_guitar;
                $input['right_piano'] = (float) $request->right_piano;
                $input['right_vibraphone'] = (float) $request->right_vibraphone;
                $input['right_trumpet'] = (float) $request->right_trumpet;
                $input['right_total'] = (float) $request->right_total;
                $input['total_imaging_position_and_focus'] = (float) $request->total_imaging_position_and_focus;

                $saveScore = CanQImagingPositionAndFocus::create($input);

                if ($saveScore) {
                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function storeListeningPleasure(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_member_class_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQListeningPleasure = CanQListeningPleasure::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanQListeningPleasure > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['listening_low_distorted'] = (float) $request->listening_low_distorted;
                $input['listening_low_vibration'] = (float) $request->listening_low_vibration;
                $input['listening_low_loudness'] = (float) $request->listening_low_loudness;
                $input['listening_low_rear_bass'] = (float) $request->listening_low_rear_bass;
                $input['listening_low_less_low_extention'] = (float) $request->listening_low_less_low_extention;
                $input['listening_low_boomy_blur_muddy'] = (float) $request->listening_low_boomy_blur_muddy;
                $input['listening_low_definition'] = (float) $request->listening_low_definition;
                $input['listening_low_total'] = (float) $request->listening_low_total;
                $input['listening_mid_bass_distorted'] = (float) $request->listening_mid_bass_distorted;
                $input['listening_mid_bass_vibration'] = (float) $request->listening_mid_bass_vibration;
                $input['listening_mid_bass_loudness'] = (float) $request->listening_mid_bass_loudness;
                $input['listening_mid_bass_position_unstable'] = (float) $request->listening_mid_bass_position_unstable;
                $input['listening_mid_bass_lr_timbre_different'] = (float) $request->listening_mid_bass_lr_timbre_different;
                $input['listening_mid_bass_stiff_thin_dry'] = (float) $request->listening_mid_bass_stiff_thin_dry;
                $input['listening_mid_bass_boomy_blur_muddy'] = (float) $request->listening_mid_bass_boomy_blur_muddy;
                $input['listening_mid_bass_definition'] = (float) $request->listening_mid_bass_definition;
                $input['listening_mid_bass_total'] = (float) $request->listening_mid_bass_total;
                $input['listening_mid_low_distorted'] = (float) $request->listening_mid_low_distorted;
                $input['listening_mid_low_loudness'] = (float) $request->listening_mid_low_loudness;
                $input['listening_mid_low_position_unstable'] = (float) $request->listening_mid_low_position_unstable;
                $input['listening_mid_low_lr_timbre_different'] = (float) $request->listening_mid_low_lr_timbre_different;
                $input['listening_mid_low_clinical_thin_dry'] = (float) $request->listening_mid_low_clinical_thin_dry;
                $input['listening_mid_low_boxy_blur_muddy'] = (float) $request->listening_mid_low_boxy_blur_muddy;
                $input['listening_mid_low_definition'] = (float) $request->listening_mid_low_definition;
                $input['listening_mid_low_total'] = (float) $request->listening_mid_low_total;
                $input['listening_mid_high_distorted'] = (float) $request->listening_mid_high_distorted;
                $input['listening_mid_high_loudness'] = (float) $request->listening_mid_high_loudness;
                $input['listening_mid_high_position_unstable'] = (float) $request->listening_mid_high_position_unstable;
                $input['listening_mid_high_lr_timbre_different'] = (float) $request->listening_mid_high_lr_timbre_different;
                $input['listening_mid_high_clinical_dry'] = (float) $request->listening_mid_high_clinical_dry;
                $input['listening_mid_high_blur_honkey'] = (float) $request->listening_mid_high_blur_honkey;
                $input['listening_mid_high_harsh_sibilance'] = (float) $request->listening_mid_high_harsh_sibilance;
                $input['listening_mid_high_total'] = (float) $request->listening_mid_high_total;
                $input['listening_high_distorted'] = (float) $request->listening_high_distorted;
                $input['listening_high_loudness'] = (float) $request->listening_high_loudness;
                $input['listening_high_lr_timbre_different'] = (float) $request->listening_high_lr_timbre_different;
                $input['listening_high_dry_clinical_metallic'] = (float) $request->listening_high_dry_clinical_metallic;
                $input['listening_high_blur_dull'] = (float) $request->listening_high_blur_dull;
                $input['listening_high_harsh_sibilance'] = (float) $request->listening_high_harsh_sibilance;
                $input['listening_high_total'] = (float) $request->listening_high_total;
                $input['listening_total'] = (float) $request->listening_total;

                $saveScore = CanQListeningPleasure::create($input);

                if ($saveScore) {
                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function storeSpectralBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_member_class_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQSpectralBalanceAndLinearity = CanQSpectralBalanceAndLinearity::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanQSpectralBalanceAndLinearity > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['spectral_balance'] = (float) $request->listening_low_distorted;
                $input['linearity'] = (float) $request->listening_low_vibration;
                $input['spectral_balance_and_linearity_total'] = (float) $request->listening_low_loudness;

                $saveScore = CanQSpectralBalanceAndLinearity::create($input);

                if ($saveScore) {
                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function storeStaging(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_member_class_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQStaging = CanQStaging::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanQStaging > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $input['event_member_class_id'] = $request->event_member_class_id;
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

                $saveScore = CanQStaging::create($input);

                if ($saveScore) {
                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function storeTonalAccuracy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_member_class_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQTonalAccuracy = CanQTonalAccuracy::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanQTonalAccuracy > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['tonal_low'] = (float) $request->tonal_low;
                $input['tonal_mid_bass'] = (float) $request->tonal_mid_bass;
                $input['tonal_mid_low'] = (float) $request->tonal_mid_low;
                $input['tonal_mid_high'] = (float) $request->tonal_mid_high;
                $input['tonal_high'] = (float) $request->tonal_high;
                $input['tonal_total'] = (float) $request->tonal_total;

                $saveScore = CanQTonalAccuracy::create($input);

                if ($saveScore) {
                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CanQScore $canQScore)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQScore = CanQScore::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanQScore > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['vision_block'] = (float) $request->vision_block;
                    $input['seating_position'] = (float) $request->seating_position;
                    $input['noise_floor'] = (float) $request->noise_floor;
                    $input['alternator_whine'] = (float) $request->alternator_whine;
                    $input['coming_late'] = (float) $request->coming_late;
                    $input['system_down'] = (float) $request->system_down;
                    $input['system_volume_level_suggested_one'] = (float) $request->system_volume_level_suggested_one;
                    $input['system_volume_level_suggested_two'] = (float) $request->system_volume_level_suggested_two;
                    $input['system_volume_level_suggested_three'] = (float) $request->system_volume_level_suggested_three;
                    $input['system_volume_level_suggested_use'] = (float) $request->system_volume_level_suggested_use;
                    $input['cheating_action'] = (float) $request->cheating_action;
                    $input['deduction_point'] = (float) $request->deduction_point;
                    $input['grand_total'] = (float) $request->grand_total;
                    $input['cheating_comment'] = $request->cheating_comment;
                    $input['deduction_comment'] = $request->deduction_comment;

                    $update = CanQScore::where('event_member_class_id', $request->event_member_class_id)->update(
                        [
                            'vision_block' => $input['vision_block'],
                            'seating_position' => $input['seating_position'],
                            'noise_floor' => $input['noise_floor'],
                            'alternator_whine' => $input['alternator_whine'],
                            'coming_late' => $input['coming_late'],
                            'system_down' => $input['system_down'],
                            'system_volume_level_suggested_one' => $input['system_volume_level_suggested_one'],
                            'system_volume_level_suggested_two' => $input['system_volume_level_suggested_two'],
                            'system_volume_level_suggested_three' => $input['system_volume_level_suggested_three'],
                            'system_volume_level_suggested_use' => $input['system_volume_level_suggested_use'],
                            'cheating_action' => $input['cheating_action'],
                            'deduction_point' => $input['deduction_point'],
                            'cheating_comment' => $input['cheating_comment'],
                            'deduction_comment' => $input['deduction_comment'],
                            'grand_total' => $input['grand_total']
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

    public function updateImagingPosition(Request $request, CanQScore $canQScore)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQImagingPositionAndFocus = CanQImagingPositionAndFocus::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanQImagingPositionAndFocus > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['left_drum'] = (float) $request->left_drum;
                    $input['left_guitar'] = (float) $request->left_guitar;
                    $input['left_piano'] = (float) $request->left_piano;
                    $input['left_vibraphone'] = (float) $request->left_vibraphone;
                    $input['left_trumpet'] = (float) $request->left_trumpet;
                    $input['left_total'] = (float) $request->left_total;
                    $input['lfctr_drum'] = (float) $request->lfctr_drum;
                    $input['lfctr_guitar'] = (float) $request->lfctr_guitar;
                    $input['lfctr_piano'] = (float) $request->lfctr_piano;
                    $input['lfctr_vibraphone'] = (float) $request->lfctr_vibraphone;
                    $input['lfctr_trumpet'] = (float) $request->lfctr_trumpet;
                    $input['lfctr_total'] = (float) $request->lfctr_total;
                    $input['center_drum'] = (float) $request->center_drum;
                    $input['center_guitar'] = $request->center_guitar;
                    $input['center_piano'] = $request->center_piano;
                    $input['center_vibraphone'] = $request->center_vibraphone;
                    $input['center_trumpet'] = $request->center_trumpet;
                    $input['center_total'] = $request->center_total;
                    $input['rhctr_drum'] = $request->rhctr_drum;
                    $input['rhctr_guitar'] = $request->rhctr_guitar;
                    $input['rhctr_piano'] = $request->rhctr_piano;
                    $input['rhctr_vibraphone'] = $request->rhctr_vibraphone;
                    $input['rhctr_trumpet'] = $request->rhctr_trumpet;
                    $input['rhctr_total'] = $request->rhctr_total;
                    $input['right_drum'] = $request->right_drum;
                    $input['right_guitar'] = $request->right_guitar;
                    $input['right_piano'] = $request->right_piano;
                    $input['right_vibraphone'] = $request->right_vibraphone;
                    $input['right_trumpet'] = $request->right_trumpet;
                    $input['right_total'] = $request->right_total;
                    $input['total_imaging_position_and_focus'] = $request->total_imaging_position_and_focus;

                    $update = CanQImagingPositionAndFocus::where('event_member_class_id', $request->event_member_class_id)->update(
                        [
                            'left_drum' => $input['left_drum'],
                            'left_guitar' => $input['left_guitar'],
                            'left_piano' => $input['left_piano'],
                            'left_vibraphone' => $input['left_vibraphone'],
                            'left_trumpet' => $input['left_trumpet'],
                            'left_total' => $input['left_total'],
                            'lfctr_drum' => $input['lfctr_drum'],
                            'lfctr_guitar' => $input['lfctr_guitar'],
                            'lfctr_piano' => $input['lfctr_piano'],
                            'lfctr_vibraphone' => $input['lfctr_vibraphone'],
                            'lfctr_trumpet' => $input['lfctr_trumpet'],
                            'lfctr_total' => $input['lfctr_total'],
                            'center_drum' => $input['center_drum'],
                            'center_guitar' => $input['center_guitar'],
                            'center_piano' => $input['center_piano'],
                            'center_vibraphone' => $input['center_vibraphone'],
                            'center_trumpet' => $input['center_trumpet'],
                            'center_total' => $input['center_total'],
                            'rhctr_drum' => $input['rhctr_drum'],
                            'rhctr_guitar' => $input['rhctr_guitar'],
                            'rhctr_piano' => $input['rhctr_piano'],
                            'rhctr_vibraphone' => $input['rhctr_vibraphone'],
                            'rhctr_trumpet' => $input['rhctr_trumpet'],
                            'rhctr_total' => $input['rhctr_total'],
                            'right_drum' => $input['right_drum'],
                            'right_guitar' => $input['right_guitar'],
                            'right_piano' => $input['right_piano'],
                            'right_vibraphone' => $input['right_vibraphone'],
                            'right_trumpet' => $input['right_trumpet'],
                            'right_total' => $input['right_total'],
                            'total_imaging_position_and_focus' => $input['total_imaging_position_and_focus']
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

    public function updateListeningPleasure(Request $request, CanQScore $canQScore)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQListeningPleasure = CanQListeningPleasure::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanQListeningPleasure > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['listening_low_distorted'] = (float) $request->listening_low_distorted;
                    $input['listening_low_vibration'] = (float) $request->listening_low_vibration;
                    $input['listening_low_loudness'] = (float) $request->listening_low_loudness;
                    $input['listening_low_rear_bass'] = (float) $request->listening_low_rear_bass;
                    $input['listening_low_less_low_extention'] = (float) $request->listening_low_less_low_extention;
                    $input['listening_low_boomy_blur_muddy'] = (float) $request->listening_low_boomy_blur_muddy;
                    $input['listening_low_definition'] = (float) $request->listening_low_definition;
                    $input['listening_low_total'] = (float) $request->listening_low_total;
                    $input['listening_mid_bass_distorted'] = (float) $request->listening_mid_bass_distorted;
                    $input['listening_mid_bass_vibration'] = (float) $request->listening_mid_bass_vibration;
                    $input['listening_mid_bass_loudness'] = (float) $request->listening_mid_bass_loudness;
                    $input['listening_mid_bass_position_unstable'] = (float) $request->listening_mid_bass_position_unstable;
                    $input['listening_mid_bass_lr_timbre_different'] = (float) $request->listening_mid_bass_lr_timbre_different;
                    $input['listening_mid_bass_stiff_thin_dry'] = $request->listening_mid_bass_stiff_thin_dry;
                    $input['listening_mid_bass_boomy_blur_muddy'] = $request->listening_mid_bass_boomy_blur_muddy;
                    $input['listening_mid_bass_definition'] = $request->listening_mid_bass_definition;
                    $input['listening_mid_bass_total'] = $request->listening_mid_bass_total;
                    $input['listening_mid_low_distorted'] = $request->listening_mid_low_distorted;
                    $input['listening_mid_low_loudness'] = $request->listening_mid_low_loudness;
                    $input['listening_mid_low_position_unstable'] = $request->listening_mid_low_position_unstable;
                    $input['listening_mid_low_lr_timbre_different'] = $request->listening_mid_low_lr_timbre_different;
                    $input['listening_mid_low_clinical_thin_dry'] = $request->listening_mid_low_clinical_thin_dry;
                    $input['listening_mid_low_boxy_blur_muddy'] = $request->listening_mid_low_boxy_blur_muddy;
                    $input['listening_mid_low_definition'] = $request->listening_mid_low_definition;
                    $input['listening_mid_low_total'] = $request->listening_mid_low_total;
                    $input['listening_mid_high_distorted'] = $request->listening_mid_high_distorted;
                    $input['listening_mid_high_loudness'] = $request->listening_mid_high_loudness;
                    $input['listening_mid_high_position_unstable'] = $request->listening_mid_high_position_unstable;
                    $input['listening_mid_high_lr_timbre_different'] = $request->listening_mid_high_lr_timbre_different;
                    $input['listening_mid_high_clinical_dry'] = $request->listening_mid_high_clinical_dry;
                    $input['listening_mid_high_blur_honkey'] = $request->listening_mid_high_blur_honkey;
                    $input['listening_mid_high_harsh_sibilance'] = $request->listening_mid_high_harsh_sibilance;
                    $input['listening_mid_high_total'] = $request->listening_mid_high_total;
                    $input['listening_high_distorted'] = $request->listening_high_distorted;
                    $input['listening_high_loudness'] = $request->listening_high_loudness;
                    $input['listening_high_lr_timbre_different'] = $request->listening_high_lr_timbre_different;
                    $input['listening_high_dry_clinical_metallic'] = $request->listening_high_dry_clinical_metallic;
                    $input['listening_high_blur_dull'] = $request->listening_high_blur_dull;
                    $input['listening_high_harsh_sibilance'] = $request->listening_high_harsh_sibilance;
                    $input['listening_high_total'] = $request->listening_high_total;
                    $input['listening_total'] = $request->listening_total;

                    $update = CanQImagingPositionAndFocus::where('event_member_class_id', $request->event_member_class_id)->update(
                        [
                            'listening_low_distorted' => $input['listening_low_distorted'],
                            'listening_low_vibration' => $input['listening_low_vibration'],
                            'listening_low_loudness' => $input['listening_low_loudness'],
                            'listening_low_rear_bass' => $input['listening_low_rear_bass'],
                            'listening_low_less_low_extention' => $input['listening_low_less_low_extention'],
                            'listening_low_boomy_blur_muddy' => $input['listening_low_boomy_blur_muddy'],
                            'listening_low_definition' => $input['listening_low_definition'],
                            'listening_low_total' => $input['listening_low_total'],
                            'listening_mid_bass_distorted' => $input['listening_mid_bass_distorted'],
                            'listening_mid_bass_vibration' => $input['listening_mid_bass_vibration'],
                            'listening_mid_bass_loudness' => $input['listening_mid_bass_loudness'],
                            'listening_mid_bass_position_unstable' => $input['listening_mid_bass_position_unstable'],
                            'listening_mid_bass_lr_timbre_different' => $input['listening_mid_bass_lr_timbre_different'],
                            'listening_mid_bass_stiff_thin_dry' => $input['listening_mid_bass_stiff_thin_dry'],
                            'listening_mid_bass_boomy_blur_muddy' => $input['listening_mid_bass_boomy_blur_muddy'],
                            'listening_mid_bass_definition' => $input['listening_mid_bass_definition'],
                            'listening_mid_bass_total' => $input['listening_mid_bass_total'],
                            'listening_mid_low_distorted' => $input['listening_mid_low_distorted'],
                            'listening_mid_low_loudness' => $input['listening_mid_low_loudness'],
                            'listening_mid_low_position_unstable' => $input['listening_mid_low_position_unstable'],
                            'listening_mid_low_lr_timbre_different' => $input['listening_mid_low_lr_timbre_different'],
                            'listening_mid_low_clinical_thin_dry' => $input['listening_mid_low_clinical_thin_dry'],
                            'listening_mid_low_boxy_blur_muddy' => $input['listening_mid_low_boxy_blur_muddy'],
                            'listening_mid_low_definition' => $input['listening_mid_low_definition'],
                            'listening_mid_low_total' => $input['listening_mid_low_total'],
                            'listening_mid_high_distorted' => $input['listening_mid_high_distorted'],
                            'listening_mid_high_loudness' => $input['listening_mid_high_loudness'],
                            'listening_mid_high_position_unstable' => $input['listening_mid_high_position_unstable'],
                            'listening_mid_high_lr_timbre_different' => $input['listening_mid_high_lr_timbre_different'],
                            'listening_mid_high_clinical_dry' => $input['listening_mid_high_clinical_dry'],
                            'listening_mid_high_blur_honkey' => $input['listening_mid_high_blur_honkey'],
                            'listening_mid_high_harsh_sibilance' => $input['listening_mid_high_harsh_sibilance'],
                            'listening_mid_high_total' => $input['listening_mid_high_total'],
                            'listening_high_distorted' => $input['listening_high_distorted'],
                            'listening_high_loudness' => $input['listening_high_loudness'],
                            'listening_high_lr_timbre_different' => $input['listening_high_lr_timbre_different'],
                            'listening_high_dry_clinical_metallic' => $input['listening_high_dry_clinical_metallic'],
                            'listening_high_blur_dull' => $input['listening_high_blur_dull'],
                            'listening_high_harsh_sibilance' => $input['listening_high_harsh_sibilance'],
                            'listening_high_total' => $input['listening_high_total'],
                            'listening_total' => $input['listening_total']
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

    public function updateSpectralBalance(Request $request, CanQScore $canQScore)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQSpectralBalanceAndLinearity = CanQSpectralBalanceAndLinearity::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanQSpectralBalanceAndLinearity > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['spectral_balance'] = (float) $request->spectral_balance;
                    $input['linearity'] = (float) $request->linearity;
                    $input['spectral_balance_and_linearity_total'] = (float) $request->spectral_balance_and_linearity_total;

                    $update = CanQSpectralBalanceAndLinearity::where('event_member_class_id', $request->event_member_class_id)->update(
                        [
                            'spectral_balance' => $input['spectral_balance'],
                            'linearity' => $input['linearity'],
                            'spectral_balance_and_linearity_total' => $input['spectral_balance_and_linearity_total']
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

    public function updateStaging(Request $request, CanQScore $canQScore)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQStaging = CanQStaging::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanQStaging > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
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
                    $input['distance_total'] = $request->distance_total;
                    $input['depth_c1_to_c2'] = $request->depth_c1_to_c2;
                    $input['depth_c2_to_c3'] = $request->depth_c2_to_c3;
                    $input['depth_total'] = $request->depth_total;
                    $input['staging_total'] = $request->staging_total;

                    $update = CanQStaging::where('event_member_class_id', $request->event_member_class_id)->update(
                        [
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
                            'staging_total' => $input['staging_total']
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

    public function updateTonalAccuracy(Request $request, CanQScore $canQScore)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanQTonalAccuracy = CanQTonalAccuracy::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanQTonalAccuracy > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['tonal_low'] = (float) $request->tonal_low;
                    $input['tonal_mid_bass'] = (float) $request->tonal_mid_bass;
                    $input['tonal_mid_low'] = (float) $request->tonal_mid_low;
                    $input['tonal_mid_high'] = (float) $request->tonal_mid_high;
                    $input['tonal_high'] = (float) $request->tonal_high;
                    $input['tonal_total'] = (float) $request->tonal_total;

                    $update = CanQTonalAccuracy::where('event_member_class_id', $request->event_member_class_id)->update(
                        [
                            'tonal_low' => $input['tonal_low'],
                            'tonal_mid_bass' => $input['tonal_mid_bass'],
                            'tonal_mid_low' => $input['tonal_mid_low'],
                            'tonal_mid_high' => $input['tonal_mid_high'],
                            'tonal_high' => $input['tonal_high'],
                            'tonal_total' => $input['tonal_total']
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
