<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EventMemberClass extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total_participants',
        'assessed',
        'form_id',
        'form_title',
        'form_assessment',
        'grand_total',
        'judge_signature',
        'participant_signature',
        'event_member_id',
        'competition_activity_id',
        'class_group_id',
        'class_grade_id',
        'car_id',
        'studio_info',
        'gear',
        'team_name',
        'victory_point',
        'status_score',
        'disqualified_status'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'total_participants' => 'integer',
        'event_member_id' => 'bigInteger',
        'competition_activity_id' => 'bigInteger',
        'class_group_id' => 'bigInteger',
        'class_grade_id' => 'bigInteger',
        'car_id' => 'bigInteger',
        'victory_point' => 'integer',
        'disqualified_status' => 'integer'
    ];

    public function eventMember()
    {
        return $this->belongsTo(EventMember::class, 'event_member_id');
    }
    public function eventJudgeMemberAssignment()
    {
        return $this->hasOne(EventJudgeMemberAssignment::class);
    }
    public function competitionActivity()
    {
        return $this->belongsTo(CompetitionActivity::class, 'competition_activity_id');
    }
    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }
    public function classGrade()
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function isMemberHaveCompeteOnceOnActivity($member_id, $competition_activity_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', $competition_activity_id)
            ->where('events.status_score_final', '=', 1)
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isMemberHaveCompeteOnceOnCanQ($member_id)
    {

        // $eventMemberClass = EventMemberClass::select(
        //     'event_member_id',
        //     'competition_activity_id'
        // )
        //     ->leftJoin('event_members', 'event_members.id', '=', 'event_member_id')
        //     ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
        //     ->where('competition_activity_id', '=', $activities[$i]['id'])
        //     ->where('users.id', '=', $request->user_id)
        //     ->count();

        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            // ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            // ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', 1)
            ->where('events.status_score_final', '=', 1)
            // ->whereNotNull('event_judge_member_assignments.id')
            // ->whereNotNull('can_q_scores.id')
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isMemberIncludedOnTopSixteenPointRank($member_id, $class_grade_id)
    {
        if ($class_grade_id === 1) {
            $user = User::select(
                'users.id AS user_id'
            )
                ->where('role_id', 6)
                ->where('can_q_consumer_point', '>', 0)
                ->orderBy('users.can_q_consumer_point', 'desc')
                ->orderBy('users.name', 'asc')
                ->limit(16)
                ->get();
        } else if ($class_grade_id === 2) {
            $user = User::select(
                'users.id AS user_id'
            )
                ->where('role_id', 6)
                ->where('can_q_consumer_point', '>', 0)
                ->orderBy('users.can_q_prosumer_point', 'desc')
                ->orderBy('users.name', 'asc')
                ->limit(16)
                ->get();
        } else {
            $user = User::select(
                'users.id AS user_id'
            )
                ->where('role_id', 6)
                ->where('can_q_consumer_point', '>', 0)
                ->orderBy('users.can_q_professional_point', 'desc')
                ->orderBy('users.name', 'asc')
                ->limit(16)
                ->get();
        }

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $user_id = (int) $arr[$i]['user_id'];
            $member_id = (int) $member_id;

            if ($user_id === $member_id) {
                return true;
            }
        }

        return false;
    }

    public function isMemberHaveCompeteOnceOnCanLoud($member_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->leftJoin('can_loud_scores', 'can_loud_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', 2)
            ->where('events.status_score_final', '=', 1)
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('can_loud_scores.id')
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isMemberHaveCompeteOnceOnCanCraft($member_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->leftJoin('can_craft_scores', 'can_craft_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', 4)
            ->where('events.status_score_final', '=', 1)
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('can_craft_scores.id')
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isMemberHaveCompeteOnceOnCanTune($member_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->leftJoin('can_tune_consumer_pyramids', 'can_tune_consumer_pyramids.event_member_class_id', '=', 'event_member_classes.id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', 5)
            ->where('events.status_score_final', '=', 1)
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('can_tune_consumer_pyramids.id')
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isMemberHaveCompeteOnceOnCanPerform($member_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->leftJoin('can_perform_scores', 'can_perform_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', 6)
            ->where('events.status_score_final', '=', 1)
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('can_perform_scores.id')
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isMemberHaveCompeteOnceOnCanDance($member_id)
    {
        $count = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
            ->leftJoin('can_dance_scores', 'can_dance_scores.event_member_class_id', '=', 'event_member_classes.id')
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', 7)
            ->where('events.status_score_final', '=', 1)
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('can_dance_scores.id')
            ->count();

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getActivityClassAssignOfMember($event_id, $member_id)
    {
        $activityMember = $this->getActivityAssignOfMember($event_id, $member_id);

        for ($x = 0; $x < sizeof($activityMember); $x++) {
            $classMember = $this->getClassAndStatusAssignOfMember($event_id, $member_id, $activityMember[$x]['competition_activity_id']);

            $count = 0;
            for ($z = 0; $z < sizeof($classMember); $z++) {
                if ($classMember[$z]['status'] === false) {
                    $count++;
                }
            }

            if ($count > 0) {
                $activityMember[$x]['activity_status'] = false;
            } else {
                $activityMember[$x]['activity_status'] = true;
            }
            $activityMember[$x]['classes'] = $classMember;
        }

        return $activityMember;
    }

    public function getActivityAssignOfMember($event_id, $member_id)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.competition_activity_id AS competition_activity_id',
            'competition_activities.name AS competition_activity_name'
        )
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_members.member_id', '=', $member_id)
            ->distinct()
            ->get();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        return $arr;
    }

    public function getClassAndStatusAssignOfMember($event_id, $member_id, $competition_activity_id)
    {
        $eventMemberClass = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id',
            'class_groups.name AS class_group_name',
            'event_judge_member_assignments.id AS assign_id',
            'event_member_classes.competition_activity_id AS competition_activity_id',
            'event_member_classes.status_score AS status_score'
            // 'event_member_classes.judge_signature AS judge_signature',
            // 'event_member_classes.participant_signature AS participant_signature'
        )
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_members.member_id', '=', $member_id)
            ->where('event_member_classes.competition_activity_id', '=', $competition_activity_id)
            ->get();

        $arr = [];

        foreach ($eventMemberClass as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            if ($arr[$i]['assign_id'] === null) {
                $arr[$i]['status'] = false;
            } else {
                $arr[$i]['status'] = true;
            }
        }

        return $arr;
    }

    public function getActivityStatusOfAssessment($event_id, $competition_activity_id)
    {
        $eventMemberClassCount = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', $competition_activity_id)
            ->count();

        if ($eventMemberClassCount > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getClassGroupAndParticipantAssessedOfEvent($event_id, $competition_activity_id, $offset, $limit, $search)
    {
        $classGroup = $this->getClassGroupAssessedOfEvent($event_id, $competition_activity_id);

        for ($i = 0; $i < sizeof($classGroup); $i++) {
            $participantAssessed = $this->getParticipantAssessedOfEvent2($event_id, $competition_activity_id, $classGroup[$i]['class_group_id'], $offset, $limit, $search);
            $classGroup[$i]['participants'] = $participantAssessed;
        }

        return $classGroup;
    }

    public function getClassGroupAssessedOfEvent($event_id, $competition_activity_id)
    {
        $classGroup = ClassGroup::select(
            'class_groups.id AS class_group_id',
            'class_groups.class_grade_id',
            'class_groups.class_category_id',
            'class_groups.name AS class_group_name',
            'class_grades.name AS class_grade_name',
            'class_categories.name AS class_category_name'

        )
            ->leftJoin('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
            ->leftJoin('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
            ->leftJoin('event_member_classes', 'event_member_classes.class_group_id', '=', 'class_groups.id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_member_classes.competition_activity_id', '=', $competition_activity_id)
            ->orderBy('event_member_classes.class_grade_id', 'asc')
            ->distinct()
            ->get();

        $arr = [];

        foreach ($classGroup as $object) {
            $arr[] = $object->toArray();
        }

        return $arr;
    }

    public function getParticipantAssessedOfEvent2($event_id, $competition_activity_id, $class_group_id, $offset, $limit, $search)
    {
        if ($competition_activity_id > 0) {
            $eventMemberClass = EventMemberClass::select(
                'event_member_classes.id AS event_member_class_id',
                'event_member_classes.event_member_id AS event_member_id',
                'event_member_classes.form_id AS form_id',
                'event_member_classes.form_title AS form_title',
                'event_member_classes.form_assessment AS form_assessment',
                'event_member_classes.judge_signature AS judge_signature',
                'event_member_classes.participant_signature AS participant_signature',
                'event_member_classes.grand_total AS score',
                'event_member_classes.team_name AS team_name',
                'event_member_classes.victory_point AS victory_point',
                'users.id AS member_id',
                'users.name AS member_name',
                'users.manual_input AS member_manual_input',
                'user_profiles.avatar AS member_avatar',
                'class_groups.name AS class_group_name',
                'cars.vehicle AS car_name',
                // 'can_q_scores.grand_total AS total',
                // DB::raw('(SELECT users.id AS judge_id FROM users WHERE users.id = event_judges.judge_id) AS judge_id'),
                DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                DB::raw('(SELECT user_profiles.avatar AS judge_avatar FROM user_profiles WHERE user_profiles.user_id = event_judges.judge_id) AS judge_avatar')
            )
                ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                // ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                ->leftJoin('cars', 'cars.id', '=', 'event_member_classes.car_id')
                ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_judge_activities.competition_activity_id', '=', $competition_activity_id)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where('event_member_classes.grand_total', '<>', null)
                ->where('event_member_classes.status_score', 1)
                // ->where(function ($query) use ($search) {
                //     $query->where('users.name', 'like', '%' . $search . '%')
                //         ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                // })
                // ->orderBy('can_q_scores.grand_total', 'desc')
                ->orderBy('event_member_classes.grand_total', 'desc')
                ->orderBy('users.name', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            $eventMemberClassCount = EventMemberClass::select(
                'event_member_classes.id AS event_member_class_id',
                'event_member_classes.event_member_id AS event_member_id',
                'event_member_classes.grand_total AS score',
                'event_member_classes.team_name AS team_name',
                'event_member_classes.victory_point AS victory_point',
                'users.id AS member_id',
                'users.name AS member_name',
                'users.manual_input AS member_manual_input',
                'user_profiles.avatar AS member_avatar',
                'class_groups.name AS class_group_name',
                // 'can_q_scores.grand_total AS total',
                DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                DB::raw('(SELECT user_profiles.avatar AS judge_avatar FROM user_profiles WHERE user_profiles.user_id = event_judges.judge_id) AS judge_avatar')
            )
                ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                // ->leftJoin('can_q_scores', 'can_q_scores.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_judge_activities.competition_activity_id', '=', $competition_activity_id)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where('event_member_classes.grand_total', '<>', null)
                ->where('event_member_classes.status_score', 1)
                // ->where(function ($query) use ($search) {
                //     $query->where('users.name', 'like', '%' . $search . '%')
                //         ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                // })
                ->count();

            $arr = [];

            foreach ($eventMemberClass as $object) {
                $arr[] = $object->toArray();
            }


            return array('data' => $arr, 'total' => $eventMemberClassCount);
        } else {
            return array('data' => [], 'total' => 0);
        }
    }

    public function getParticipantAssessedOfEvent($event_id, $competition_activity_id, $class_group_id, $offset, $limit, $search)
    {
        if ($competition_activity_id === 1) { // CAN Q
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
                ->where('event_judge_activities.competition_activity_id', '=', 1)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->orderBy('can_q_scores.grand_total', 'desc')
                ->orderBy('users.name', 'asc')
                ->offset($offset)
                ->limit($limit)
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
                ->where('event_judge_activities.competition_activity_id', '=', 1)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->count();

            $arr = [];

            foreach ($eventMemberClass as $object) {
                $arr[] = $object->toArray();
            }

            return array('data' => $arr, 'total' => $eventMemberClassCount);
        } else if ($competition_activity_id === 2) { // CAN LOUD
            $eventMemberClass = EventMemberClass::select(
                'event_member_classes.id AS event_member_class_id',
                'event_member_classes.event_member_id AS event_member_id',
                'users.name AS member_name',
                'user_profiles.avatar AS member_avatar',
                'class_groups.name AS class_group_name',
                'can_loud_scores.total AS total',
                DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
            )
                ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                ->leftJoin('can_loud_scores', 'can_loud_scores.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_judge_activities.competition_activity_id', '=', 2)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->orderBy('can_loud_scores.total', 'desc')
                ->orderBy('users.name', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            $eventMemberClassCount = EventMemberClass::select(
                'event_member_classes.id AS event_member_class_id',
                'event_member_classes.event_member_id AS event_member_id',
                'users.name AS member_name',
                'user_profiles.avatar AS member_avatar',
                'class_groups.name AS class_group_name',
                'can_loud_scores.total AS total',
                DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
            )
                ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                ->leftJoin('can_loud_scores', 'can_loud_scores.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_judge_activities.competition_activity_id', '=', 2)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->count();

            $arr = [];

            foreach ($eventMemberClass as $object) {
                $arr[] = $object->toArray();
            }

            return array('data' => $arr, 'total' => $eventMemberClassCount);
        } else if ($competition_activity_id === 3) { // CAN JAM
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
                ->where('event_judge_activities.competition_activity_id', '=', 3)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->orderBy('can_jam_score_histories.total', 'desc')
                ->orderBy('users.name', 'asc')
                ->offset($offset)
                ->limit($limit)
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
                ->where('event_judge_activities.competition_activity_id', '=', 3)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->count();

            $arr = [];

            foreach ($eventMemberClass as $object) {
                $arr[] = $object->toArray();
            }

            return array('data' => $arr, 'total' => $eventMemberClassCount);
        } else if ($competition_activity_id === 4) { // CAN CRAFT
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
                ->where('event_judge_activities.competition_activity_id', '=', 4)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->orderBy('can_craft_scores.total', 'desc')
                ->orderBy('users.name', 'asc')
                ->offset($offset)
                ->limit($limit)
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
                ->where('event_judge_activities.competition_activity_id', '=', 4)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->count();

            $arr = [];

            foreach ($eventMemberClass as $object) {
                $arr[] = $object->toArray();
            }

            return array('data' => $arr, 'total' => $eventMemberClassCount);
        } else if ($competition_activity_id === 5) { // CAN TUNE
            $count = EventMemberClass::select(
                'event_member_classes.id AS event_member_class_id'
            )
                ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->where('event_members.event_id', '=', $event_id)
                ->where('event_member_classes.competition_activity_id', '=', 5)
                ->where('event_member_classes.class_grade_id', '=', 1)
                ->count();

            if ($count >= 2) {
                if ($class_group_id === 90) {
                    $eventMemberClass = EventMemberClass::select(
                        'event_member_classes.id AS event_member_class_id',
                        'event_member_classes.event_member_id AS event_member_id',
                        'users.name AS member_name',
                        'user_profiles.avatar AS member_avatar',
                        'class_groups.name AS class_group_name',
                        'can_tune_consumer_pyramids.total AS total',
                        DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                        DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
                    )
                        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                        ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                        ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->leftJoin('can_tune_consumer_pyramids', 'can_tune_consumer_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                        ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                        ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                        ->where('event_judge_activities.competition_activity_id', '=', 5)
                        ->where('event_judges.event_id', '=', $event_id)
                        ->where('event_members.event_id', '=', $event_id)
                        ->where('class_groups.id', '=', $class_group_id)
                        ->where(function ($query) use ($search) {
                            $query->where('users.name', 'like', '%' . $search . '%')
                                ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                        })
                        ->orderBy('can_tune_consumer_pyramids.total', 'desc')
                        ->orderBy('users.name', 'asc')
                        ->offset($offset)
                        ->limit($limit)
                        ->get();

                    $eventMemberClassCount = EventMemberClass::select(
                        'event_member_classes.id AS event_member_class_id',
                        'event_member_classes.event_member_id AS event_member_id',
                        'users.name AS member_name',
                        'user_profiles.avatar AS member_avatar',
                        'class_groups.name AS class_group_name',
                        'can_tune_consumer_pyramids.total AS total',
                        DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                        DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
                    )
                        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                        ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                        ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->leftJoin('can_tune_consumer_pyramids', 'can_tune_consumer_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                        ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                        ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                        ->where('event_judge_activities.competition_activity_id', '=', 5)
                        ->where('event_judges.event_id', '=', $event_id)
                        ->where('event_members.event_id', '=', $event_id)
                        ->where('class_groups.id', '=', $class_group_id)
                        ->where(function ($query) use ($search) {
                            $query->where('users.name', 'like', '%' . $search . '%')
                                ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                        })
                        ->count();

                    $arr = [];

                    foreach ($eventMemberClass as $object) {
                        $arr[] = $object->toArray();
                    }
                    return array('data' => $arr, 'total' => $eventMemberClassCount);
                } else if ($class_group_id === 91) {
                    $eventMemberClass = EventMemberClass::select(
                        'event_member_classes.id AS event_member_class_id',
                        'event_member_classes.event_member_id AS event_member_id',
                        'users.name AS member_name',
                        'user_profiles.avatar AS member_avatar',
                        'class_groups.name AS class_group_name',
                        'can_tune_prosumer_pyramids.total AS total',
                        DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                        DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
                    )
                        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                        ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                        ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->leftJoin('can_tune_prosumer_pyramids', 'can_tune_prosumer_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                        ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                        ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                        ->where('event_judge_activities.competition_activity_id', '=', 5)
                        ->where('event_judges.event_id', '=', $event_id)
                        ->where('event_members.event_id', '=', $event_id)
                        ->where('class_groups.id', '=', $class_group_id)
                        ->where(function ($query) use ($search) {
                            $query->where('users.name', 'like', '%' . $search . '%')
                                ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                        })
                        ->orderBy('can_tune_prosumer_pyramids.total', 'desc')
                        ->orderBy('users.name', 'asc')
                        ->offset($offset)
                        ->limit($limit)
                        ->get();

                    $eventMemberClassCount = EventMemberClass::select(
                        'event_member_classes.id AS event_member_class_id',
                        'event_member_classes.event_member_id AS event_member_id',
                        'users.name AS member_name',
                        'user_profiles.avatar AS member_avatar',
                        'class_groups.name AS class_group_name',
                        'can_tune_prosumer_pyramids.total AS total',
                        DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                        DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
                    )
                        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                        ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                        ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->leftJoin('can_tune_prosumer_pyramids', 'can_tune_prosumer_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                        ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                        ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                        ->where('event_judge_activities.competition_activity_id', '=', 5)
                        ->where('event_judges.event_id', '=', $event_id)
                        ->where('event_members.event_id', '=', $event_id)
                        ->where('class_groups.id', '=', $class_group_id)
                        ->where(function ($query) use ($search) {
                            $query->where('users.name', 'like', '%' . $search . '%')
                                ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                        })
                        ->count();

                    $arr = [];

                    foreach ($eventMemberClass as $object) {
                        $arr[] = $object->toArray();
                    }
                    return array('data' => $arr, 'total' => $eventMemberClassCount);
                } else if ($class_group_id === 92) {
                    $eventMemberClass = EventMemberClass::select(
                        'event_member_classes.id AS event_member_class_id',
                        'event_member_classes.event_member_id AS event_member_id',
                        'users.name AS member_name',
                        'user_profiles.avatar AS member_avatar',
                        'class_groups.name AS class_group_name',
                        'can_tune_professional_pyramids.total AS total',
                        DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                        DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
                    )
                        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                        ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                        ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->leftJoin('can_tune_professional_pyramids', 'can_tune_professional_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                        ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                        ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                        ->where('event_judge_activities.competition_activity_id', '=', 5)
                        ->where('event_judges.event_id', '=', $event_id)
                        ->where('event_members.event_id', '=', $event_id)
                        ->where('class_groups.id', '=', $class_group_id)
                        ->where(function ($query) use ($search) {
                            $query->where('users.name', 'like', '%' . $search . '%')
                                ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                        })
                        ->orderBy('can_tune_professional_pyramids.total', 'desc')
                        ->orderBy('users.name', 'asc')
                        ->offset($offset)
                        ->limit($limit)
                        ->get();

                    $eventMemberClassCount = EventMemberClass::select(
                        'event_member_classes.id AS event_member_class_id',
                        'event_member_classes.event_member_id AS event_member_id',
                        'users.name AS member_name',
                        'user_profiles.avatar AS member_avatar',
                        'class_groups.name AS class_group_name',
                        'can_tune_professional_pyramids.total AS total',
                        DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                        DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
                    )
                        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                        ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                        ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                        ->leftJoin('can_tune_professional_pyramids', 'can_tune_professional_pyramids.event_member_class_id', '=', 'event_member_classes.id')
                        ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                        ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                        ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                        ->where('event_judge_activities.competition_activity_id', '=', 5)
                        ->where('event_judges.event_id', '=', $event_id)
                        ->where('event_members.event_id', '=', $event_id)
                        ->where('class_groups.id', '=', $class_group_id)
                        ->where(function ($query) use ($search) {
                            $query->where('users.name', 'like', '%' . $search . '%')
                                ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                        })
                        ->count();

                    $arr = [];

                    foreach ($eventMemberClass as $object) {
                        $arr[] = $object->toArray();
                    }
                    return array('data' => $arr, 'total' => $eventMemberClassCount);
                } else {
                    return array('data' => [], 'total' => 0);
                }
            } else {
                return array('data' => [], 'total' => 0);
            }
        } else if ($competition_activity_id === 6) { // CAN PERFORM
            $eventMemberClass = EventMemberClass::select(
                'event_member_classes.id AS event_member_class_id',
                'event_member_classes.event_member_id AS event_member_id',
                'users.name AS member_name',
                'user_profiles.avatar AS member_avatar',
                'class_groups.name AS class_group_name',
                'can_perform_scores.grand_total AS total',
                DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
            )
                ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                ->leftJoin('can_perform_scores', 'can_perform_scores.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_judge_activities.competition_activity_id', '=', 6)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->orderBy('can_perform_scores.grand_total', 'desc')
                ->orderBy('users.name', 'asc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            $eventMemberClassCount = EventMemberClass::select(
                'event_member_classes.id AS event_member_class_id',
                'event_member_classes.event_member_id AS event_member_id',
                'users.name AS member_name',
                'user_profiles.avatar AS member_avatar',
                'class_groups.name AS class_group_name',
                'can_perform_scores.grand_total AS total',
                DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id) AS judge_name'),
                DB::raw('(SELECT user_profiles.avatar FROM user_profiles WHERE user_profiles.user_id = users.id AND users.id = event_judges.judge_id) AS judge_avatar')
            )
                ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
                ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
                ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
                ->leftJoin('can_perform_scores', 'can_perform_scores.event_member_class_id', '=', 'event_member_classes.id')
                ->leftJoin('class_groups', 'class_groups.id', '=', 'event_member_classes.class_group_id')
                ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'event_members.member_id')
                ->where('event_judge_activities.competition_activity_id', '=', 6)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->count();

            $arr = [];

            foreach ($eventMemberClass as $object) {
                $arr[] = $object->toArray();
            }

            return array('data' => $arr, 'total' => $eventMemberClassCount);
        } else if ($competition_activity_id === 7) { // CAN DANCE
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
                ->where('event_judge_activities.competition_activity_id', '=', 6)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->orderBy('can_dance_scores.grand_total', 'desc')
                ->orderBy('users.name', 'asc')
                ->offset($offset)
                ->limit($limit)
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
                ->where('event_judge_activities.competition_activity_id', '=', 6)
                ->where('event_judges.event_id', '=', $event_id)
                ->where('event_members.event_id', '=', $event_id)
                ->where('class_groups.id', '=', $class_group_id)
                ->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('(SELECT users.name AS judge_name FROM users WHERE users.id = event_judges.judge_id)'), 'like', '%' . $search . '%');
                })
                ->count();

            $arr = [];

            foreach ($eventMemberClass as $object) {
                $arr[] = $object->toArray();
            }

            return array('data' => $arr, 'total' => $eventMemberClassCount);
        } else {
            return array('data' => [], 'total' => 0);
        }
    }
}
