<?php

namespace App\Http\Controllers;

use App\EventActivityClassForm;
use App\FormGenerator;
use Illuminate\Http\Request;

class EventActivityClassFormController extends Controller
{
    public $successStatus = 200;

    public function listActivityFormsAvailable(Request $request)
    {
        $formGenerator = FormGenerator::select(
            'id',
            'user_id',
            'title',
            'form_assessment',
            'audio_player_ids',
            'created_at',
            'updated_at'
        )
            // ->where('user_id', '=', $request->user_id)
            ->where('status_delete', '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $formGeneratorCount = FormGenerator::select(
            'id',
            'title'
        )
            ->where('status_delete', '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            })
            ->count();

        $eventActivityClassForms = EventActivityClassForm::select(
            'event_activity_class_forms.event_id',
            'event_activity_class_forms.competition_activity_id',
            'event_activity_class_forms.class_grade_id',
            'event_activity_class_forms.form_generator_id'
        )
            ->where('event_id', $request->event_id)
            ->where('competition_activity_id', $request->competition_activity_id)
            ->where('class_grade_id', $request->class_grade_id)
            ->get();

        $arrEventActivityClassForms = [];

        foreach ($eventActivityClassForms as $object) {
            $arrEventActivityClassForms[] = $object->toArray();
        }

        $arr = [];

        foreach ($formGenerator as $object) {
            $arr[] = $object->toArray();
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $arr[$i]['status'] = false;
            for ($j = 0; $j < sizeof($arrEventActivityClassForms); $j++) {
                if ($arr[$i]['id'] == $arrEventActivityClassForms[$j]['form_generator_id']) {
                    $arr[$i]['status'] = true;
                    array_splice($arrEventActivityClassForms, $j, 1);
                    break;
                }
            }
        }

        return response()->json(['data' => $arr, 'total' => $formGeneratorCount, '$arrEventActivityClassForms' => $arrEventActivityClassForms]);
    }


    public function assignFormId(Request $request)
    {
        $count = EventActivityClassForm::where('event_id', $request->event_id)
        ->where('competition_activity_id', $request->competition_activity_id)
        ->where('class_grade_id', $request->class_grade_id)
        ->where('form_generator_id', $request->form_generator_id)
        ->count();

        if ($count > 0) {
            return response()->json(['status' => 'failed', 'message' => 'Form already assigned with this class'], $this->successStatus);
        } else {
            $input['event_id'] = $request->event_id;
            $input['competition_activity_id'] = $request->competition_activity_id;
            $input['class_grade_id'] = $request->class_grade_id;
            $input['form_generator_id'] = $request->form_generator_id;

            $save = EventActivityClassForm::create($input);
        }

        if ($save) {
            return response()->json(['status' => 'success', 'message' => 'class grade assigned to form successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'failed assign class grade to form'], $this->successStatus);
        }
    }

    public function deleteAssignFormId(Request $request)
    {
        $eventActivityClassForm = EventActivityClassForm::where('event_id', $request->event_id)
            ->where('competition_activity_id', $request->competition_activity_id)
            ->where('class_grade_id', $request->class_grade_id)
            ->where('form_generator_id', $request->form_generator_id)
            ->first();

        if ($eventActivityClassForm) {
            $deleteEventActivityClassForm = EventActivityClassForm::where('event_id', $request->event_id)
            ->where('competition_activity_id', $request->competition_activity_id)
            ->where('class_grade_id', $request->class_grade_id)
            ->where('form_generator_id', $request->form_generator_id)
            ->delete();

            if ($deleteEventActivityClassForm) {
                return response()->json(['status' => 'success', 'message' => 'form assignment deleted successfully'], $this->successStatus);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'failed delete form assignment'], $this->successStatus);
            }

        } else {
            return response()->json(['status' => 'failed', 'message' => 'event activity class form not found'], $this->successStatus);
        }
    }
}
