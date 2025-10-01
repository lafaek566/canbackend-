<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CanJamScoreHistory;
use App\CanJamScore;
use App\ClassGroup;
use App\EventMemberClass;

class CanJamScoreController extends Controller
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNotNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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
            'can_jam_score_histories.total AS total',
            DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
            DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('can_jam_score_histories', 'can_jam_score_histories.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('can_jam_scores', 'can_jam_scores.can_jam_score_history_id', '=', 'can_jam_score_histories.id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
            ->whereNull('can_jam_scores.id')
            ->where('event_judge_activities.competition_activity_id', '=', 3)
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

    public function getMaxScore(Request $request)
    {
        $classGroup = ClassGroup::join('event_member_classes', 'event_member_classes.class_group_id', '=', 'class_groups.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)->first();

        // return response()->json(['status' => 'success', 'classGroup' => $classGroup], $this->successStatus);

        if ($classGroup->class_group_id === 74 || $classGroup->class_group_id === 75) {
            return response()->json(['status' => 'success', 'max' => 120], $this->successStatus);
        } else if ($classGroup->class_group_id === 76 || $classGroup->class_group_id === 77) {
            return response()->json(['status' => 'success', 'max' => 126], $this->successStatus);
        } else if ($classGroup->class_group_id === 78 || $classGroup->class_group_id === 79) {
            return response()->json(['status' => 'success', 'max' => 132], $this->successStatus);
        } else if ($classGroup->class_group_id === 80 || $classGroup->class_group_id === 81) {
            return response()->json(['status' => 'success', 'max' => 138], $this->successStatus);
        } else if ($classGroup->class_group_id === 82 || $classGroup->class_group_id === 83) {
            return response()->json(['status' => 'success', 'max' => 144], $this->successStatus);
        } else if ($classGroup->class_group_id === 84 || $classGroup->class_group_id === 85) {
            return response()->json(['status' => 'success', 'max' => 147], $this->successStatus);
        } else if ($classGroup->class_group_id === 86 || $classGroup->class_group_id === 87) {
            return response()->json(['status' => 'success', 'max' => 150], $this->successStatus);
        } else if ($classGroup->class_group_id === 88 || $classGroup->class_group_id === 89) {
            return response()->json(['status' => 'success', 'max' => 'unlimited'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'not found'], $this->successStatus);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_member_class_id' => 'required',
            'db_score' => 'required',
            'system_down' => 'required',
            'deduction_point' => 'required',
            'deduction_comment' => 'required',
            'time_start' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else {
                $date = date('Y-m-d H:i:s');

                $input['event_member_class_id'] = $request->event_member_class_id;
                $input['db_score'] = (float) $request->db_score;
                $input['system_down'] = (float) $request->system_down;
                $input['deduction_point'] = (float) $request->deduction_point;
                $input['deduction_comment'] = $request->deduction_comment;
                $input['time_start'] = $request->time_start;
                $input['time_end'] = $date;

                $input['total'] = $input['db_score'] + $input['system_down'] + $input['deduction_point'];

                $saveScore = CanJamScoreHistory::create($input);

                if ($saveScore) {
                    $this->assess($request->event_member_class_id);

                    return response()->json(['status' => 'success', 'message' => 'participant assessed successfully'], $this->successStatus);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'participant assessed failed'], 200);
                }
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'event not found'], 200);
        }
    }

    public function assess($event_member_class_id)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $event_member_class_id)
            ->first();

        if ($event) {
            $date = date('Y-m-d');

            if ($event->date_start > $date) {
                return response()->json(['status' => 'failed', 'message' => 'event still upcoming, assessment decline'], 200);
            } else if ($event->status_score_final === 1) {
                return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
            } else {
                $canJamScore = CanJamScore::select(
                    'can_jam_scores.id'
                )
                    ->join('can_jam_score_histories', 'can_jam_score_histories.id', '=', 'can_jam_scores.can_jam_score_history_id')
                    ->where('can_jam_score_histories.event_member_class_id', '=', $event_member_class_id)
                    ->get();

                $countCanJamScore = $canJamScore->count();

                $maxCanJamScoreHistory = CanJamScoreHistory::where('event_member_class_id', $event_member_class_id)->orderBy('total', 'desc')->first();

                if ($countCanJamScore > 0) {
                    $id = $canJamScore[0]->id;

                    $update = CanJamScore::where('id', $id)->update(
                        [
                            'can_jam_score_history_id' => $maxCanJamScoreHistory->id
                        ]
                    );

                    // if ($update) {
                    //     return response()->json(['status' => 'success', 'message' => 'assessed successfully'], 200);
                    // } else {
                    //     return response()->json(['status' => 'failed', 'message' => 'assess failed'], 401);
                    // }
                } else {
                    $input['can_jam_score_history_id'] = $maxCanJamScoreHistory->id;

                    $saveScore = CanJamScore::create($input);

                    // if ($saveScore) {
                    //     return response()->json(['status' => 'success', 'message' => 'assessed successfully'], 200);
                    // } else {
                    //     return response()->json(['status' => 'failed', 'message' => 'assess failed'], 401);
                    // }
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
    public function update(Request $request, CanJamScoreHistory $canJamScoreHistory)
    {
        $event = Event::join('event_members', 'event_members.event_id', '=', 'events.id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('event_member_classes.id', '=', $request->event_member_class_id)
            ->first();

        $countCanJamScoreHistory = CanJamScoreHistory::select('id')->where('id', '=', $request->id)->where('event_member_class_id', '=', $request->event_member_class_id)->count();
        if ($event) {
            if ($countCanJamScoreHistory > 0) {
                if ($event->status_score_final === 1) {
                    return response()->json(['status' => 'failed', 'message' => 'assessment have been finalized, assessment decline'], 200);
                } else {
                    $input['db_score'] = (float) $request->db_score;
                    $input['system_down'] = (float) $request->system_down;
                    $input['deduction_point'] = (float) $request->deduction_point;
                    $input['deduction_comment'] = $request->deduction_comment;

                    $input['total'] = $input['db_score'] + $input['system_down'] + $input['deduction_point'];

                    $update = CanJamScoreHistory::where('id', $request->id)->update(
                        [
                            'db_score' => $input['db_score'],
                            'system_down' => $input['system_down'],
                            'deduction_point' => $input['deduction_point'],
                            'deduction_comment' => $input['deduction_comment'],
                            'total' => $input['total']
                        ]
                    );

                    if ($update) {
                        $this->assess($request->event_member_class_id);

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
