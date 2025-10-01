<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CanCraftScore;
use App\CanCraftProExtreme;
use function GuzzleHttp\json_decode;

class CanCraftScoreController extends Controller
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
            'can_craft_scores.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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
            'can_craft_scores.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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
            'can_craft_scores.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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
            'can_craft_scores.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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
            'can_craft_scores.total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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
            'can_craft_scores.total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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
            'can_craft_scores.total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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
            'can_craft_scores.total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_craft_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 4)
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

    public function listProExtreme(Request $request)
    {
        $canCraftProExtreme = CanCraftProExtreme::select(
            'can_craft_pro_extremes.id AS can_craft_pro_extreme_id',
            'items',
            'comments',
            'point',
            'event_member_class_id'
        )
            ->where('event_member_class_id', '=', $request->event_member_class_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $canCraftProExtremeCount = CanCraftProExtreme::select(
            'can_craft_pro_extremes.id AS can_craft_pro_extreme_id',
            'items',
            'comments',
            'point',
            'event_member_class_id'
        )
            ->where('event_member_class_id', '=', $request->event_member_class_id)
            ->count();

        $arr = [];

        foreach ($canCraftProExtreme as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $canCraftProExtremeCount], 200);
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

        $countCanQScore = CanCraftScore::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanQScore > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $date = date('Y-m-d H:i:s');

                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['connection_quality'] = (float) $request->connection_quality;
                $input['main_fuse_value'] = (float) $request->main_fuse_value;
                $input['wire_length'] = (float) $request->wire_length;
                $input['fuse_value'] = (float) $request->fuse_value;
                $input['product_mounting'] = (float) $request->product_mounting;
                $input['overall_wiring'] = (float) $request->overall_wiring;
                $input['overall_workmanship_safety_factor'] = (float) $request->overall_workmanship_safety_factor;
                $input['protection_quality'] = (float) $request->protection_quality;
                $input['main_fuse_connection_quality'] = (float) $request->main_fuse_connection_quality;
                $input['wire_penetration'] = (float) $request->wire_penetration;
                $input['mounting_quality'] = (float) $request->mounting_quality;
                $input['fuse_block'] = (float) $request->fuse_block;
                $input['all_main_equipment_connection_quality'] = (float) $request->all_main_equipment_connection_quality;
                $input['overall_workmanship_quality'] = (float) $request->overall_workmanship_quality;
                $input['battery_housing'] = (float) $request->battery_housing;
                $input['mounting_quality_of_front_fuse'] = (float) $request->mounting_quality_of_front_fuse;
                $input['additional_ground_wire'] = (float) $request->additional_ground_wire;
                $input['detail_workmanship'] = (float) $request->detail_workmanship;
                $input['overall_design_and_ideas'] = (float) $request->overall_design_and_ideas;
                $input['deduction_point'] = (float) $request->deduction_point;
                $input['deduction_comment'] = $request->deduction_comment;
                $input['time_start'] = $request->time_start;
                $input['time_end'] = $date;

                $input['total'] = $input['connection_quality'] + $input['main_fuse_value'] + $input['wire_length'] + $input['fuse_value'] + $input['product_mounting'] + $input['overall_wiring']
                    + $input['overall_workmanship_safety_factor'] + $input['protection_quality'] + $input['main_fuse_connection_quality'] + $input['wire_penetration'] + $input['mounting_quality']
                    + $input['fuse_block'] + $input['all_main_equipment_connection_quality'] + $input['overall_workmanship_quality'] + $input['battery_housing'] + $input['mounting_quality_of_front_fuse']
                    + $input['additional_ground_wire'] + $input['detail_workmanship'] + $input['overall_design_and_ideas'] + $input['deduction_point'];

                if ($request->can_craft_pro_extreme_status === 1) {
                    $arrProExtreme = json_decode($request->pro_extreme_items);

                    $totalPointProExtreme = 0;
                    for ($i = 0; $i < sizeof($arrProExtreme); $i++) {
                        $point = (int) $arrProExtreme[$i]['point'];
                        $totalPointProExtreme = $totalPointProExtreme + $point;
                    }

                    $input['total'] = $input['total'] + $totalPointProExtreme;
                }

                $saveScore = CanCraftScore::create($input);

                if ($saveScore) {
                    $this->updateCanCraftProExtreme($request);
                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    protected function updateCanCraftProExtreme(Request $request)
    {
        $arrProExtreme = json_decode($request->pro_extreme_items);

        for ($i = 0; $i < sizeof($arrProExtreme); $i++) {
            $id = $arrProExtreme[$i]['can_craft_pro_extreme_id'];
            $comment_judge = $arrProExtreme[$i]['comment_judge'];
            $point = (int) $arrProExtreme[$i]['point'];

            $update = CanCraftProExtreme::where('id', $id)->update(
                [
                    'comment_judge' => $comment_judge,
                    'point' => $point
                ]
            );
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

        $countCanCraftScore = CanCraftScore::select('id')->where('id', '=', $request->id)->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanCraftScore > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['event_member_class_id'] = $request->event_member_class_id;
                    $input['connection_quality'] = (float) $request->connection_quality;
                    $input['main_fuse_value'] = (float) $request->main_fuse_value;
                    $input['wire_length'] = (float) $request->wire_length;
                    $input['fuse_value'] = (float) $request->fuse_value;
                    $input['product_mounting'] = (float) $request->product_mounting;
                    $input['overall_wiring'] = (float) $request->overall_wiring;
                    $input['overall_workmanship_safety_factor'] = (float) $request->overall_workmanship_safety_factor;
                    $input['protection_quality'] = (float) $request->protection_quality;
                    $input['main_fuse_connection_quality'] = (float) $request->main_fuse_connection_quality;
                    $input['wire_penetration'] = (float) $request->wire_penetration;
                    $input['mounting_quality'] = (float) $request->mounting_quality;
                    $input['fuse_block'] = (float) $request->fuse_block;
                    $input['all_main_equipment_connection_quality'] = (float) $request->all_main_equipment_connection_quality;
                    $input['overall_workmanship_quality'] = (float) $request->overall_workmanship_quality;
                    $input['battery_housing'] = (float) $request->battery_housing;
                    $input['mounting_quality_of_front_fuse'] = (float) $request->mounting_quality_of_front_fuse;
                    $input['additional_ground_wire'] = (float) $request->additional_ground_wire;
                    $input['detail_workmanship'] = (float) $request->detail_workmanship;
                    $input['overall_design_and_ideas'] = (float) $request->overall_design_and_ideas;
                    $input['deduction_point'] = (float) $request->deduction_point;
                    $input['deduction_comment'] = $request->deduction_comment;

                    $input['total'] = $input['connection_quality'] + $input['main_fuse_value'] + $input['wire_length'] + $input['fuse_value'] + $input['product_mounting'] + $input['overall_wiring']
                        + $input['overall_workmanship_safety_factor'] + $input['protection_quality'] + $input['main_fuse_connection_quality'] + $input['wire_penetration'] + $input['mounting_quality']
                        + $input['fuse_block'] + $input['all_main_equipment_connection_quality'] + $input['overall_workmanship_quality'] + $input['battery_housing'] + $input['mounting_quality_of_front_fuse']
                        + $input['additional_ground_wire'] + $input['detail_workmanship'] + $input['overall_design_and_ideas'] + $input['deduction_point'];

                    if ($request->can_craft_pro_extreme_status === 1) {
                        $arrProExtreme = json_decode($request->pro_extreme_items);

                        $totalPointProExtreme = 0;
                        for ($i = 0; $i < sizeof($arrProExtreme); $i++) {
                            $point = (int) $arrProExtreme[$i]['point'];
                            $totalPointProExtreme = $totalPointProExtreme + $point;
                        }

                        $input['total'] = $input['total'] + $totalPointProExtreme;
                    }

                    $update = CanCraftScore::where('id', $request->id)->update(
                        [
                            'connection_quality' => $input['connection_quality'],
                            'main_fuse_value' => $input['main_fuse_value'],
                            'wire_length' => $input['wire_length'],
                            'fuse_value' => $input['fuse_value'],
                            'product_mounting' => $input['product_mounting'],
                            'overall_wiring' => $input['overall_wiring'],
                            'overall_workmanship_safety_factor' => $input['overall_workmanship_safety_factor'],
                            'protection_quality' => $input['protection_quality'],
                            'main_fuse_connection_quality' => $input['main_fuse_connection_quality'],
                            'wire_penetration' => $input['wire_penetration'],
                            'mounting_quality' => $input['mounting_quality'],
                            'fuse_block' => $input['fuse_block'],
                            'all_main_equipment_connection_quality' => $input['all_main_equipment_connection_quality'],
                            'overall_workmanship_quality' => $input['overall_workmanship_quality'],
                            'battery_housing' => $input['battery_housing'],
                            'mounting_quality_of_front_fuse' => $input['mounting_quality_of_front_fuse'],
                            'additional_ground_wire' => $input['additional_ground_wire'],
                            'detail_workmanship' => $input['detail_workmanship'],
                            'overall_design_and_ideas' => $input['overall_design_and_ideas'],
                            'deduction_point' => $input['deduction_point'],
                            'total' => $input['total']
                        ]
                    );

                    if ($update) {
                        $this->updateCanCraftProExtreme($request);
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
