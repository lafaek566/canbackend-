<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventType;

class EventTypeController extends Controller
{
    public $successStatus = 200;

    public function listAllEventType(Request $request)
    {
        $eventType = EventType::select(
            'id',
            'name',
            'factor'
        )
            ->get();

        $eventTypeCount = EventType::select(
            'id',
            'name',
            'factor'
        )
            ->count();

        $arr = [];

        foreach ($eventType as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $eventTypeCount], 200);
    }

}
