<?php

namespace App\Http\Controllers;

use App\EventZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class EventZoneController extends Controller
{
    public function getEventZone()
    {
        $event_zones = EventZone::join('countries', 'event_zones.country_id', '=', 'countries.id')
            ->select(
                'event_zones.id',
                'event_zones.zone_name',
                'event_zones.country_id',
                'countries.name as country_name'
            )
            ->get();
        $arr = [];
        $arr[] = [
            'id' => 0,
            'zone_name' => 'No Zone',
            'country_id' => null,
            'country_name' => null,
        ];
        foreach ($event_zones as $object) {
            $arr[] = $object->toArray();
        }



        return response()->json($arr);
    }

    /**
     * Get all event zone
     *
     * @return Response
     */

    public function getEventZoneWithTrashed()
    {
        $event_zones = EventZone::join('countries', 'event_zones.country_id', '=', 'countries.id')
            ->select(
                'event_zones.id',
                'event_zones.zone_name',
                'event_zones.country_id',
                'event_zones.deleted_at',
                'countries.name as country_name'
            )
            ->withTrashed()
            ->get();

        $arr = [];

        foreach ($event_zones as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json($arr);
    }
    /**
     * Get all event zone
     *
     * @return Response
     */

    public function addEventZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone_name' => 'required',
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();

        $save_event_zone = EventZone::create($input);

        if ($save_event_zone) {
            return response()->json([
                'status' => 'success',
                'message' => 'created successfully'
            ], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    public function getEventZoneWithId(EventZone $eventZone)
    {
        return response()->json($eventZone->toArray());
    }

    public function updateEventZone(Request $request, $id)
    {

        $update = EventZone::where('id', $id)->update([
            'zone_name' => $request->zone_name,
            'country_id' => $request->country_id,
        ]);

        if ($update) {
            return response()->json([
                'status' => 'success',
                'message' => 'updated successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'update failed'
            ], 400);
        }
    }

    public function deleteEventZone(Request $request)
    {
        $delete = EventZone::where('id', $request->id)->delete();

        if ($delete) {
            $success = ['status' => 'success', 'message' => 'deleted successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'delete failed'];
        }

        return response()->json($success, 200);
    }

    public function restoreEventZone(Request $request)
    {
        $restore = EventZone::where('id', $request->id)->withTrashed()->restore();

        if ($restore) {
            $success = ['status' => 'success', 'message' => 'restored successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'restore failed'];
        }

        return response()->json($success, 200);
    }

    public function getEventZoneWithCountryId($id)
    {

        $event_zones = EventZone::join('countries', 'event_zones.country_id', '=', 'countries.id')
            ->where('countries.id', $id)
            ->select('event_zones.*')
            ->get()
            ->toArray();

        if (!empty($event_zones)) {
            $result = [];
            $result[] = [
                "id" => 0,
                "country_id" => (int) $id,
                "zone_name" => "No Zone",
            ];
            foreach ($event_zones as $event_zone) {
                $result[] = [
                    "id" => $event_zone['id'],
                    "country_id" => $event_zone['country_id'],
                    "zone_name" => $event_zone['zone_name'],
                ];
            }


            return response()->json($result);
        }
        $result[] = [
            "id" => 0,
            "country_id" => (int) $id,
            "zone_name" => "No Zone",
        ];
        return response()->json($result);
    }
}
