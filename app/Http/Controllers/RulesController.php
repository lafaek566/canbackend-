<?php

namespace App\Http\Controllers;

use App\Rules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RulesController extends Controller
{
    public $successStatus = 200;

    public function show(Request $request)
    {
        $rules = Rules::select(
            'id',
            'title',
            'description',
            'link',
            'alt'
        )
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('link', 'like', '%' . $request->search . '%')
                    ->orWhere('alt', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $rulesCount = Rules::where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('link', 'like', '%' . $request->search . '%')
                    ->orWhere('alt', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($rules as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $rulesCount]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'link' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $store = Rules::create($request->all());

        if ($store) {
            return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'link' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $update = Rules::where('id', $request->id)->update(
            [
                'title' => $request->title,
                'description' => $request->description,
                'link' => $request->link,
                'alt' => $request->alt
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    public function delete(Request $request)
    {
        $delete = Rules::where('id', $request->id)->delete();

        if ($delete) {
            $success = ['status' => 'success', 'message' => 'deleted successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'delete failed'];
        }

        return response()->json($success, $this->successStatus);
    }
}
