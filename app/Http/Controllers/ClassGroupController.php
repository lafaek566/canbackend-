<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClassGroup;
use Illuminate\Support\Facades\Validator;

class ClassGroupController extends Controller
{
    public $successStatus = 200;

    public function listAllByGradeCountryCategory(Request $request)
    {

        if ($request->class_country_id !== null && $request->class_country_id != "null" && $request->class_category_id !== null && $request->event_id !== null) {
            $classGroup = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.event_id', '=', $request->event_id)
                ->get();

            $classGroupCount = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.event_id', '=', $request->event_id)
                ->count();

            $arr = [];

            foreach ($classGroup as $object) {
                $arr[] = $object->toArray();
            }

            return response()->json(['data' => $arr, 'total' => $classGroupCount], 200);
        } else if ($request->association_id !== null && $request->association_id != 'null' && $request->class_category_id !== null && $request->event_id !== null) {
            $classGroup = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'associations.name AS association_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('associations', 'associations.id', '=', 'class_groups.association_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.association_id', '=', $request->association_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.event_id', '=', $request->event_id)
                ->get();

            $classGroupCount = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'associations.name AS association_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('associations', 'associations.id', '=', 'class_groups.association_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.association_id', '=', $request->association_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.event_id', '=', $request->event_id)
                ->count();

            $arr = [];

            foreach ($classGroup as $object) {
                $arr[] = $object->toArray();
            }

            return response()->json(['data' => $arr, 'total' => $classGroupCount], 200);
        } else if ($request->class_country_id !== null && $request->class_category_id !== null && $request->event_id !== null) {
            $classGroup = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.event_id', '=', $request->event_id)
                ->get();

            $classGroupCount = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.event_id', '=', $request->event_id)
                ->count();

            $arr = [];

            foreach ($classGroup as $object) {
                $arr[] = $object->toArray();
            }

            return response()->json(['data' => $arr, 'total' => $classGroupCount, 'tes'], 200);
        } else if ($request->class_country_id !== null && $request->class_country_id != 'null' && $request->class_category_id !== null) {
            $classGroup = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->get();

            $classGroupCount = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->count();

            $arr = [];

            foreach ($classGroup as $object) {
                $arr[] = $object->toArray();
            }

            return response()->json(['data' => $arr, 'total' => $classGroupCount], 200);
        } else if ($request->association_id !== null && $request->association_id != 'null' && $request->class_category_id !== null) {
            $classGroup = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'associations.name AS assoication_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('associations', 'associations.id', '=', 'class_groups.association_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.association_id', '=', $request->association_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->get();

            $classGroupCount = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->count();

            $arr = [];

            foreach ($classGroup as $object) {
                $arr[] = $object->toArray();
            }

            return response()->json(['data' => $arr, 'total' => $classGroupCount], 200);
        } else if ($request->class_country_id !== null && $request->class_category_id !== null) {
            $classGroup = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->get();

            $classGroupCount = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name',
                'competition_activities.name AS competition_activity_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->join('class_countries', 'class_countries.id', '=', 'class_groups.class_country_id')
                ->join('class_categories', 'class_categories.id', '=', 'class_groups.class_category_id')
                ->join('competition_activities', 'competition_activities.id', '=', 'class_categories.competition_activity_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->count();

            $arr = [];

            foreach ($classGroup as $object) {
                $arr[] = $object->toArray();
            }

            return response()->json(['data' => $arr, 'total' => $classGroupCount], 200);
        } else {
            $classGroup = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->get();

            $classGroupCount = ClassGroup::select(
                'class_groups.id AS id',
                'class_groups.name AS name',
                'class_grades.name AS class_grade_name',
                'class_countries.name AS class_country_name',
                'class_categories.name AS class_category_name'
            )
                ->join('class_grades', 'class_grades.id', '=', 'class_groups.class_grade_id')
                ->where('class_groups.class_grade_id', '=', $request->class_grade_id)
                ->where('class_groups.class_country_id', '=', $request->class_country_id)
                ->where('class_groups.class_category_id', '=', $request->class_category_id)
                ->where('class_groups.disabled', '=', $request->disabled)
                ->count();

            $arr = [];

            foreach ($classGroup as $object) {
                $arr[] = $object->toArray();
            }

            return response()->json(['data' => $arr, 'total' => $classGroupCount], 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_grade_id' => 'required',
            'class_country_id' => 'required',
            'class_category_id' => 'required',
            'class_group_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $classGroups = ClassGroup::select(
            'id',
            'name'
        )
            ->where('class_grade_id', $request->class_grade_id)
            ->where('class_country_id', $request->class_country_id)
            ->where('class_category_id', $request->class_category_id)
            ->where('disabled', 0)
            ->where('event_id', null)
            ->get();

        $classGroupName = trim($request->class_group_name, "");
        foreach ($classGroups as $classGroup) {
            if (strtolower($classGroup->name) == strtolower($classGroupName)) {
                return response()->json(['status' => 'failed', 'message' => 'class group name already used'], $this->successStatus);
            }
        }

        $input['class_grade_id'] = $request->class_grade_id;
        $input['class_country_id'] = $request->class_country_id;
        $input['class_category_id'] = $request->class_category_id;
        $input['name'] = $request->class_group_name;

        $save = ClassGroup::create($input);

        if ($save) {
            return response()->json(['status' => 'success', 'message' => 'class group successfully added'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'failed adding class group'], $this->successStatus);
        }
    }

    public function disabled(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_group_id' => 'required',
            'disabled' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $save = ClassGroup::where('id', $request->class_group_id)->update([
            'disabled' => $request->disabled
        ]);

        $message = $request->disabled == 0 ? 'activated' : 'disabled';

        if ($save) {
            return response()->json(['status' => 'success', 'message' => 'class group successfully ' . $message], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'failed ' . $message . ' class group'], $this->successStatus);
        }
    }
}
