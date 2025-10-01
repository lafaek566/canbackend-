<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventJudge extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'judge_id',
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
        'judge_id' => 'bigInteger'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class, "user_id", "judge_id");
    }

    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    public function getEventJudgeStatusAssign($event_judge_id)
    {
        $countEventJudgeAssigned = EventJudge::select('event_judges.id AS id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.event_judge_id', '=', 'event_judges.id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_judge_activity_id', '=', 'event_judge_activities.id')
            ->where('event_judges.id', '=', $event_judge_id)
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('event_judge_activities.id')
            ->count();

        if ($countEventJudgeAssigned > 0) {
            return true;
        } else {
            return false;
        }
    }
}
