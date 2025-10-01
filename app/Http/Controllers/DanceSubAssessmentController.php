<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DanceSubAssessment;

class DanceSubAssessmentController extends Controller
{
    public $successStatus = 200;

    public function getDanceSubAssessmentByDanceMajorAspectId(Request $request)
    {
        $danceSubAssessment = DanceSubAssessment::select(
            'id',
            'name'
        )
            ->where('dance_major_aspect_id', $request->dance_major_aspect_id)
            ->get();

        $arr = [];

        foreach ($danceSubAssessment as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }
}
