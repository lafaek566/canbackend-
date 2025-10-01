<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventActivityClassForm extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'competition_activity_id',
        'class_grade_id',
        'form_generator_id',
        'form_generator_ids'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_id' => 'bigInteger',
        'competition_activity_id' => 'bigInteger',
        'class_grade_id' => 'bigInteger',
        'form_generator_id' => 'bigInteger'
    ];

    public function eventId()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function competitionActivity()
    {
        return $this->belongsTo(CompetitionActivity::class, 'competition_activity_id');
    }

    public function classGrade()
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }

    // public function formGeneratorId()
    // {
    //     return $this->belongsTo(FormGenerator::class, 'form_generator_id');
    // }

    public function getCompetitionActivityClassGrade($competition_activity_id)
    {
        // $arrCompetitionActivity = $competition_activity;
        $arrClassGrade = [];

        // for ($j = 0; $j < sizeof($arrCompetitionActivity); $j++) {
        //     $id = (int) $arrCompetitionActivity[$j]['id'];

        $competitionActivityClassGrade = CompetitionActivityClassGrade::where('competition_activity_id', $competition_activity_id)->get();

        // $arrClassGrade = [];
        for ($k = 0; $k < sizeof($competitionActivityClassGrade); $k++) {
            $classGradeId = (int) $competitionActivityClassGrade[$k]['class_grade_id'];

            $classGrade = ClassGrade::where('id', $classGradeId)->first();
            array_push($arrClassGrade, $classGrade['id']);
        }

        // $arrCompetitionActivity[$j]['class_grade_ids'] = $arrClassGrade;
        // }

        return $arrClassGrade;
    }

    public function getCompetitionActivityClassName($competition_activity_id)
    {
        $arrClassGrade = [];

        $competitionActivityClassGrade = CompetitionActivityClassGrade::where('competition_activity_id', $competition_activity_id)->get();

        // $arrClassGrade = [];
        for ($k = 0; $k < sizeof($competitionActivityClassGrade); $k++) {
            $classGradeId = (int) $competitionActivityClassGrade[$k]['class_grade_id'];

            $classGrade = ClassGrade::where('id', $classGradeId)->first();
            array_push($arrClassGrade, $classGrade['name']);
        }

        return $arrClassGrade;
    }

    public function getStatusAssignment($event_id, $competition_activity_id, $class_grade_id, $form_generator_id)
    {
        if ($event_id == null && $competition_activity_id == null && $class_grade_id == null && $form_generator_id == null) {
            return false;
        } else {
            return true;
        }
    }
}
