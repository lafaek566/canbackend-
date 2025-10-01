<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\FormGenerator;
use Illuminate\Support\Facades\Auth;

class FormGeneratorController extends Controller
{
    public $successStatus = 200;

    public function listAllFormGenerator(Request $request)
    {
        $formGenerator = FormGenerator::select(
            'id',
            'user_id',
            'title',
            'form_assessment',
            'status_public',
            'audio_player_ids',
            'created_at',
            'updated_at'
        )
            //            ->where('user_id', '=', $request->user_id)
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

        $arr = [];

        foreach ($formGenerator as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $formGeneratorCount]);
    }

    public function listAllFormGeneratorByStatus(Request $request)
    {
        $formGenerator = FormGenerator::select(
            'id',
            'user_id',
            'title',
            'status_public',
            'form_assessment',
            'audio_player_ids',
            'created_at',
            'updated_at'
        )
            ->where('status_delete', '=', 0)
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->where("status_public", "=", 1)
            ->get();

        $formGeneratorCount = FormGenerator::select(
            'id',
            'title'
        )
            ->where('status_delete', '=', 0)
            ->where("status_public", "=", 1)
            ->count();

        $arr = [];

        foreach ($formGenerator as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $formGeneratorCount]);
    }

    public function detailFormGenerator(Request $request)
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
            ->where('id', '=', $request->form_id)
            ->where('status_delete', '=', 0)
            ->get();

        return response()->json($formGenerator[0]);
    }

    // public function detailFormGeneratorUser(Request $request)
    // {
    //     $formGenerator = EventMemberClass::select(
    //         'id',
    //         'user_id',
    //         'title',
    //         'form_assessment',
    //     )
    //         ->where('id', '=', $request->form_id)
    //         ->where('status_delete', '=', 0)
    //         ->get();

    //     return response()->json($formGenerator[0]);
    // }


    public function storeFormGenerator(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required',
            'form_assessment' => 'required',
            'audio_player_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $request->all();

        $id = FormGenerator::create($input)->id;
        $input['created_at'] = Carbon::now()->toDateTimeString();
        $input['updated_at'] = Carbon::now()->toDateTimeString();

        if ($id) {
            $input['id'] = $id;
            return response()->json(['status' => 'success', 'message' => 'created successfully', 'result' => $input], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    public function updateStatusFormGenerator(Request $request, FormGenerator $formGenerator)
    {
        $update = $formGenerator->update([
            "status_public" => !$formGenerator->status_public
        ]);

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    public function updateFormGenerator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_id' => 'required',
            'title' => 'required',
            'form_assessment' => 'required',
            'audio_player_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $update = FormGenerator::where('id', $request->form_id)->update(
            [
                'title' => $request->title,
                'form_assessment' => $request->form_assessment,
                'audio_player_ids' => $request->audio_player_ids
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    public function deleteFormGenerator(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'form_id' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 401);
        // }

        $update = FormGenerator::where('id', $request->form_id)->update(
            [
                'status_delete' => 1,
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'delete success'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'delete failed'], 401);
        }
    }
}
