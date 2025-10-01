<?php

namespace App\Http\Controllers;

use App\EventActivityForm;
use Illuminate\Http\Request;

class EventActivityFormController extends Controller
{
    public $successStatus = 200;

    public function listActivityFormsAvailable(Request $request)
    {
        $activityForms = EventActivityForm::select(
            'form_generators.id AS event_form_generator_id',
            'form_generators.title AS form_generators_title',
            'form_generators.form_assessment AS form_generators_form_assessment',
            'form_generators.created_at AS form_generators_created_at',
            'form_generators_updated_at AS form_generators_updated_at'
        )
            ->where('form_generators.status_delete', '=', '0')
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $arr = [];

        foreach ($activityForms as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => 0, 'request' => $request]);
    }

    public function store(Request $request)
    {
        $input['event_competition_activity_id'] = $request->event_competition_activity_id;
        $input['event_form_generator_id'] = $request->event_form_generator_id;

        $save = EventActivityForm::create($input);

        if ($save) {
            return response()->json(['status' => 'success', 'message' => 'activity assigned to form successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'failed assign activity to form'], $this->successStatus);
        }
    }
}
