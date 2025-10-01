<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CanDanceScore;

class CanDanceScoreController extends Controller
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
            'can_dance_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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
            'can_dance_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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
            'can_dance_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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
            'can_dance_scores.grand_total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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
            'can_dance_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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
            'can_dance_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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
            'can_dance_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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
            'can_dance_scores.grand_total AS total'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_dance_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 7)
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

        $countCanDanceScore = CanDanceScore::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else if ($countCanDanceScore > 0) {
                return response()->json(['status' => 'failed', 'message' => 'assessment decline because this participant have been assessed in this class'], 200);
            } else {
                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['theme'] = (float) $request->theme;
                $input['choreography'] = (float) $request->choreography;
                $input['costume'] = (float) $request->costume;
                $input['movement_quality'] = (float) $request->movement_quality;
                $input['dynamic_and_speed'] = (float) $request->dynamic_and_speed;
                $input['lower_body_activity'] = (float) $request->lower_body_activity;
                $input['upper_body_activity'] = (float) $request->upper_body_activity;
                $input['facial_expression'] = (float) $request->facial_expression;
                $input['team_confidence'] = (float) $request->team_confidence;
                $input['impact_to_audience'] = (float) $request->impact_to_audience;
                $input['dance_total'] = (float) $request->dance_total;
                $input['deduction_point'] = (float) $request->deduction_point;
                $input['grand_total'] = (float) $request->grand_total;
                $input['deduction_comment'] = $request->deduction_comment;
                $input['time_start'] = $request->time_start;
                $input['time_end'] = $date;

                $saveScore = CanDanceScore::create($input);

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

    public function update(Request $request, CanDanceScore $canDanceScore)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanDanceScore = CanDanceScore::select('id')->where('event_member_class_id', '=', $request->event_member_class_id)->count();

        if ($event) {
            if ($countCanDanceScore > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['theme'] = (float) $request->theme;
                    $input['choreography'] = (float) $request->choreography;
                    $input['costume'] = (float) $request->costume;
                    $input['movement_quality'] = (float) $request->movement_quality;
                    $input['dynamic_and_speed'] = (float) $request->dynamic_and_speed;
                    $input['lower_body_activity'] = (float) $request->lower_body_activity;
                    $input['upper_body_activity'] = (float) $request->upper_body_activity;
                    $input['facial_expression'] = (float) $request->facial_expression;
                    $input['team_confidence'] = (float) $request->team_confidence;
                    $input['impact_to_audience'] = (float) $request->impact_to_audience;
                    $input['dance_total'] = (float) $request->dance_total;
                    $input['deduction_point'] = (float) $request->deduction_point;
                    $input['grand_total'] = (float) $request->grand_total;
                    $input['deduction_comment'] = $request->deduction_comment;

                    $update = CanDanceScore::where('event_member_class_id', $request->event_member_class_id)->update(
                        [
                            'theme' => $input['theme'],
                            'choreography' => $input['choreography'],
                            'costume' => $input['costume'],
                            'movement_quality' => $input['movement_quality'],
                            'balance' => $input['balance'],
                            'dynamic_and_speed' => $input['dynamic_and_speed'],
                            'lower_body_activity' => $input['lower_body_activity'],
                            'upper_body_activity' => $input['upper_body_activity'],
                            'facial_expression' => $input['facial_expression'],
                            'team_confidence' => $input['team_confidence'],
                            'impact_to_audience' => $input['impact_to_audience'],
                            'dance_total' => $input['dance_total'],
                            'deduction_point' => $input['deduction_point'],
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
}
