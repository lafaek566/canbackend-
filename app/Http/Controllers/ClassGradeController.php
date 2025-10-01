<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClassGrade;
use App\CompetitionActivity;

class ClassGradeController extends Controller
{
    public $successStatus = 200;

    public function listAll()
    {
        $classGrade = ClassGrade::select(
            'id',
            'name',
            'alias'
        )
            ->get();

        $arr = [];

        foreach ($classGrade as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr], 200);
    }

    public function getByCompetitionActivity(CompetitionActivity $competitionActivity)
    {
        $data = $competitionActivity->classGrades;

        return response()->json(['data' => $data], $this->successStatus);
    }
}
