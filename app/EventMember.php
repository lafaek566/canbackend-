<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventMember extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'member_id',
        'competition_label'
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
        'event_id' => 'bigInteger',
        'member_id' => 'bigInteger'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function getCompetitionsByEventIdAndMemberId($event_id, $member_id)
    {
        $eventMember = EventMember::where('event_id', '=', $event_id)->where('member_id', '=', $member_id)->first();

        $eventMemberClass = EventMemberClass::select(
            'competition_activities.competition_id AS competition_id',
            'competitions.title AS competition_title'
        )
            ->join('competition_activities', 'competition_activities.id', '=', 'event_member_classes.competition_activity_id')
            ->join('competitions', 'competitions.id', '=', 'competition_activities.competition_id')
            ->where('event_member_id', $eventMember->id)
            ->distinct()
            ->get();

        return $eventMemberClass;
    }

    public function getMemberStatusAssignOfEventAndClass($event_member_id)
    {
        $countEventMemberClassNotAssigned = EventMemberClass::select(
            'event_member_classes.id AS event_member_class_id'
        )
        ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
        ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
        ->where('event_member_classes.event_member_id', '=', $event_member_id)
        ->whereNull('event_judge_member_assignments.id')
        ->count();

        if ($countEventMemberClassNotAssigned > 0) {
            return false;
        } else {
            return true;
        }
    }
}
