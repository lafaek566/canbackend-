<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClassCategory;
use App\ClassGroup;

class ClassCategoryController extends Controller
{
    public $successStatus = 200;

    public function listAllByCompetitionActivityId(Request $request)
    {
        if ($request->class_grade_id == null && $request->class_country_id == null) {
            $classCategory = ClassCategory::select(
                'class_categories.id AS id',
                'class_categories.name AS name',
                'competition_activities.name AS competition_activity_name',
                'competition_activities.id AS competition_activity_id'
            )
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_categories.competition_activity_id', '=', $request->competition_activity_id)
                ->get();

            $classCategoryCount = ClassCategory::select(
                'class_categories.id AS id',
                'class_categories.name AS name',
                'competition_activities.name AS competition_activity_name',
                'competition_activities.id AS competition_activity_id'
            )
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_categories.competition_activity_id', '=', $request->competition_activity_id)
                ->count();


            $arr = [];

            foreach ($classCategory as $object) {
                $arr[] = $object->toArray();
            }
        } else {
            $classCategory = ClassCategory::select(
                'class_categories.id AS id',
                'class_categories.name AS name',
                'competition_activities.name AS competition_activity_name',
                'competition_activities.id AS competition_activity_id'
            )
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_categories.competition_activity_id', '=', $request->competition_activity_id)
                ->get();

            $arr = array();

            $event_id = $request->event_id == null ? $request->event_id : null;

            foreach ($classCategory as $c) {

                if ($c->id == 19 || $c->id == 20 || $c->id == 21 || $c->id == 22 || $c->id == 23 || $c->id == 24 || $c->id == 25 || $c->id == 26 || $c->id == 33) {
                    if ($request->event_id !== null) {
                        array_push($arr, $c);
                    }
                }


                $classGroupCount = ClassGroup::select('id')
                    ->where('class_grade_id', $request->class_grade_id)
                    ->where('class_country_id', $request->class_country_id)
                    ->where('class_category_id', $c->id)
                    ->where('event_id', null)
                    ->where('disabled', 0)
                    ->count();

                if ($classGroupCount > 0) {
                    array_push($arr, $c);
                }
            }

            $classCategoryCount = sizeof($arr);
        }

        return response()->json(['data' => $arr, 'total' => $classCategoryCount], 200);
    }
}
