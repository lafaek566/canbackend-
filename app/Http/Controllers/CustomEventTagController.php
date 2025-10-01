<?php

namespace App\Http\Controllers;

use App\CustomEventTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomEventTagController extends Controller
{
    public function getCustomEventTags()
    {
        $custom_event_tags = CustomEventTag::all();

        $arr = [];

        foreach ($custom_event_tags as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json($arr);
    }

    public function getCustomEventTagWithTrashed()
    {
        $custom_event_tags = CustomEventTag::withTrashed()->get();

        $arr = [];

        foreach ($custom_event_tags as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json($arr);
    }

    public function addCustomEventTag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'tag_name' => 'required|unique:custom_event_tags,tag_name'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();

        $save_custom_event_tag = CustomEventTag::create($input);

        if ($save_custom_event_tag) {
            return response()->json([
                'status' => 'success',
                'message' => 'created successfully'
            ], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    public function getCustomEventTagWithId(CustomEventTag $customEventTag)
    {
        return response()->json($customEventTag->toArray());
    }

    public function updateCustomEventTag(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'tag_name' => 'required|unique:custom_event_tags,tag_name'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $update = CustomEventTag::where('id', $id)->update([
            'year' => $request->year,
            'tag_name' => $request->tag_name,
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

    public function deleteCustomEventTag(Request $request)
    {
        $delete = CustomEventTag::where('id', $request->id)->delete();

        if ($delete) {
            $success = ['status' => 'success', 'message' => 'deleted successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'delete failed'];
        }

        return response()->json($success, 200);
    }

    public function restoreCustomEventTag(Request $request)
    {
        $restore = CustomEventTag::where('id', $request->id)->withTrashed()->restore();

        if ($restore) {
            $success = ['status' => 'success', 'message' => 'restored successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'restore failed'];
        }

        return response()->json($success, 200);
    }

    public function getCustomEventTagWithYear($tagYear)
    {
        $custom_event_tags = CustomEventTag::where('year', $tagYear)->get();

        $arr = [];

        foreach ($custom_event_tags as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json($arr);
    }
}
