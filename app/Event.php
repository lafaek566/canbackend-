<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use stdClass;

class Event extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'banner',
        'title',
        'description',
        'recap',
        'date_start',
        'date_end',
        'time_start',
        'time_end',
        'date_time_start',
        'date_time_end',
        'location',
        'contact_name',
        'contact_phone',
        'status_can_final',
        'status_score_final',
        'use_custom_class',
        'event_type_id',
        'event_country_id',
        'zone',
        'tag',
        'vp_multiplier',
        'association_id',
        'event_countries_id',
        'competition_activity'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'date_time_start',
        'date_time_end'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status_can_final' => 'integer',
        'status_score_final' => 'integer',
        'use_custom_class' => 'integer',
        'event_type_id' => 'bigInteger',
        'event_country_id' => 'bigInteger',
        'vp_multiplier' => 'integer',
        'zone' => 'integer',
        'tag' => 'integer',
    ];

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function getEventStatusPastByDateStart($date_start)
    {
        $date = date('Y-m-d');

        if ($date_start > $date) {
            return false;
        } else {
            return true;
        }
    }

    public function getCompetitionsOfEvent($competition_activity)
    {
        $arrCompetitionActivity = json_decode($competition_activity, true);
        $arrCompetitions = [];
        $arrCheck = [];

        for ($i = 0; $i < sizeof($arrCompetitionActivity); $i++) {
            $id = (int) $arrCompetitionActivity[$i]['id'];
            if ($id >= 1 && $id <= 6) {
                $check = 'audio';

                if (!in_array($check, $arrCheck)) {
                    $competition = Competition::where('id', 1)->first();
                    array_push($arrCompetitions, $competition);
                    array_push($arrCheck, $check);
                }
            } else if ($id === 7) {
                $check = 'dance';

                if (!in_array($check, $arrCheck)) {
                    $competition = Competition::where('id', 2)->first();
                    array_push($arrCompetitions, $competition);
                    array_push($arrCheck, $check);
                }
            } else if ($id === 8) {
                $check = 'photography';

                if (!in_array($check, $arrCheck)) {
                    $competition = Competition::where('id', 3)->first();
                    array_push($arrCompetitions, $competition);
                    array_push($arrCheck, $check);
                }
            }
        }

        return $arrCompetitions;
    }

    public function getStatusEventByEventId($event_id)
    {
        $keterangan_participants = '';
        $keterangan_judges = '';
        $keterangan_assignments = '';
        $status_event = false;
        $status_participants = false;
        $status_judges = false;
        $status_assignments = false;

        $countEventMember = EventMember::select(
            'id'
        )
            ->where('event_id', '=', $event_id)
            ->count();

        if ($countEventMember === 0) {
            $keterangan_participants = 'No participant registered in this event';
            $status_participants = false;
        } else {

            $countEventMember = EventMember::select(
                'event_members.id AS id'
            )
                ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
                ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
                ->whereNull('event_judge_member_assignments.id')
                ->where('event_members.event_id', '=', $event_id)
                ->count();

            if ($countEventMember > 0) {
                if ($countEventMember === 1) {
                    $keterangan_participants = 'There is 1 participant class who has not been assigned with a judge';
                    $status_participants = false;
                } else {
                    $keterangan_participants = 'There are ' . $countEventMember . ' participant classes who has not been assigned with a judge';
                    $status_participants = false;
                }
            } else {
                $keterangan_participants = 'All participants have been assign with judges';
                $status_participants = true;
            }
        }

        $countEventJudge = EventJudge::select(
            'id'
        )
            ->where('event_id', '=', $event_id)
            ->count();

        if ($countEventJudge === 0) {
            $keterangan_judges = 'No judge registered in this event';
            $status_judges = false;
        } else {

            $countEventJudgeAll = EventJudge::select(
                'event_judges.id AS id'
            )
                ->where('event_judges.event_id', '=', $event_id)
                ->count();

            $countEventJudgeAssignment = EventJudge::select(
                'event_judges.id AS id'
            )
                ->join('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
                ->join('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
                ->where('event_judges.event_id', '=', $event_id)
                ->count();


            $countEventJudge = $countEventJudgeAll - $countEventJudgeAssignment;

            if ($countEventJudge > 0) {
                if ($countEventJudge === 1) {
                    $keterangan_judges = 'There is 1 judge who has not been assigned with a participant';
                    $status_judges = false;
                } else {
                    $keterangan_judges = 'There are ' . $countEventJudge . ' judges who has not been assigned with a participant';
                    $status_judges = false;
                }
            } else {
                $keterangan_judges = 'All judges have been assign with participants';
                $status_judges = true;
            }
        }

        $countEventMemberAssignment = EventJudgeMemberAssignment::select(
            'event_judge_member_assignments.id AS id'
        )
            ->join('event_member_classes', 'event_member_classes.id', '=', 'event_judge_member_assignments.event_member_class_id')
            ->join('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->join('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->join('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->where('event_members.event_id', '=', $event_id)
            ->where('event_judges.event_id', '=', $event_id)
            ->count();

        if ($countEventMemberAssignment === 0) {
            $keterangan_assignments = 'No assignment found in this event';
            $status_assignments = false;
        } else {
            if ($countEventMemberAssignment === 1) {
                $keterangan_assignments = 'There is 1 assignment found in this event';
            } else {
                $keterangan_assignments = 'There are ' . $countEventMemberAssignment . ' assignments found in this event';
            }

            $status_assignments = true;
        }

        if ($status_participants === true && $status_judges === true && $status_assignments === true) {
            $status_event = true;
        } else {
            $status_event = false;
        }

        return array(
            'keterangan_participants' => $keterangan_participants,
            'keterangan_judges' => $keterangan_judges,
            'keterangan_assignments' => $keterangan_assignments,
            'status_event' => $status_event
        );
    }

    public function getCustomClassesOfEvent($event_id, $arrCompetitionActivityClassGrade)
    {
        $custom_classes = [];

        for ($i = 0; $i < sizeof($arrCompetitionActivityClassGrade); $i++) {
            $act = $arrCompetitionActivityClassGrade[$i];

            $activity['id'] = $act['id'];
            $activity['name'] = $act['name'];
            $activity['is_active'] = true;

            $classCategoryId = $this->getClassCategoryId($act);

            $classGrades = [];

            for ($j = 0; $j < sizeof($act['class_grade_ids']); $j++) {
                $c = $act['class_grade_ids'][$j];

                $class_names = [];

                if ($c) {
                    $class['class_id'] = $c;

                    $classGrade = ClassGrade::where('id', $c)->first();
                    if ($classGrade) {
                        $class['name'] = $classGrade->name;
                    }

                    $classGroups = ClassGroup::where('class_grade_id', $c)->where('event_id', $event_id)->where('class_category_id', $classCategoryId)->get();

                    if ($classGroups) {

                        foreach ($classGroups as $classGroup) {
                            $eventMemberClass = EventMemberClass::where('class_group_id', $classGroup->id)->first();

                            $classNameObject = new stdClass();
                            $classNameObject->removable = $eventMemberClass ? false : true;
                            $classNameObject->class_group_id = $classGroup->id;
                            $classNameObject->name = $classGroup->name;

                            array_push($class_names, $classNameObject);
                        }
                    }
                }

                $class['class_names'] = $class_names;

                array_push($classGrades, $class);
            }

            $activity['classes'] = $classGrades;

            array_push($custom_classes, $activity);
        }

        return $custom_classes;
    }

    public function getClassCategoryId($activities)
    {
        // Hardcode class category id
        switch ($activities['id']) {
            case 1:
                return 19;
                break;
            case 2:
                return 20;
                break;
            case 5:
                return 25;
                break;
            case 6:
                return 21;
                break;
            case 4:
                return 22;
                break;
            case 7:
                return 23;
                break;
            case 9:
                return 33;
                break;
        }
    }

    public function tags()
    {
        return $this->belongsToMany(CustomEventTag::class, 'event_tag_groups', 'event_id', 'tag_id')->whereNull('event_tag_groups.deleted_at');
    }
}
