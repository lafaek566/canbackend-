<?php

namespace App\Http\Controllers;

use App\ClassCategory;
use App\ClassCountry;
use App\ClassGrade;
use App\ClassGroup;
use Illuminate\Http\Request;
use App\CompetitionActivity;
use App\EventActivityClassForm;
use stdClass;

class CompetitionActivityController extends Controller
{
    public $successStatus = 200;

    public function listAll(Request $request)
    {
        $competitionActivity = CompetitionActivity::select(
            'competition_activities.id AS id',
            'name',
            'competition_id',
            'competitions.title AS competition_title'
        )
            ->join('competitions', 'competitions.id', '=', 'competition_activities.competition_id')
            ->get();

        $competitionActivityCount = CompetitionActivity::select(
            'competition_activities.id AS id',
            'name',
            'competition_id',
            'competitions.title AS competition_title'
        )
            ->join('competitions', 'competitions.id', '=', 'competition_activities.competition_id')
            ->count();

        $arr = [];

        $eventActivityClassForm = new EventActivityClassForm();

        foreach ($competitionActivity as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $arr[$i]['class_grade_ids'] = $eventActivityClassForm->getCompetitionActivityClassGrade($arr[$i]['id']);
            $arr[$i]['class_grade_names'] = $eventActivityClassForm->getCompetitionActivityClassName($arr[$i]['id']);
        }


        return response()->json(['data' => $arr, 'total' => $competitionActivityCount], 200);
    }

    public function listAllClasses(Request $request)
    {
        $competitionActivity = CompetitionActivity::select(
            'id',
            'name'
            // 'competition_id',
            // 'competitions.title AS competition_title'
        )
            // ->join('competitions', 'competitions.id', '=', 'competition_activities.competition_id')
            ->whereNotIn('id', [3, 4, 5, 7, 8, 10])
            ->get();

        $arr = array();
        foreach ($competitionActivity as $c) {
            $competitionActivity = new CompetitionActivity();
            $classesArrayPerActivity = $competitionActivity->getAllClassesPerActivity($c->id);
            array_push($arr, $classesArrayPerActivity);
        }

        return response()->json(['data' => $arr], 200);
    }

    public function listAllClassesPerActivity(Request $request)
    {
        $competitionActivity = new CompetitionActivity();
        $classesArrayPerActivity = $competitionActivity->getAllClassesPerActivity($request->competition_activity_id);

        return response()->json($classesArrayPerActivity, 200);
    }
}
