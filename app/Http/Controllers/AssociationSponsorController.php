<?php

namespace App\Http\Controllers;

use App\Association;
use App\AssociationSponsor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssociationSponsorController extends Controller
{
    public function listAll()
    {
        $associationSponsor = AssociationSponsor::select(
            'association_sponsors.id AS id',
            'association_sponsors.user_id AS user_id',
            'association_sponsors.association_id AS association_id',
            'users.name AS name',
            'user_profiles.avatar AS avatar',
            'associations.name AS association_name',
            'associations.logo AS association_logo'
        )
            ->join('users', 'users.id', '=', 'association_sponsors.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('associations', 'associations.id', '=', 'association_sponsors.association_id')
            ->get();

        $arr = [];

        foreach ($associationSponsor as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    public function listAllByDetail(Association $association)
    {
        $associationSponsor = AssociationSponsor::select(
            'association_sponsors.id AS id',
            'association_sponsors.user_id AS user_id',
            'association_sponsors.association_id AS association_id',
            'users.name AS name',
            'user_profiles.avatar AS avatar',
            'associations.name AS association_name',
            'associations.logo AS association_logo'
        )
            ->join('users', 'users.id', '=', 'association_sponsors.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('associations', 'associations.id', '=', 'association_sponsors.association_id')
            ->where('association_sponsors.association_id', '=', $association->id)
            ->get();

        $arr = [];

        foreach ($associationSponsor as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'association_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()], 401);
        }

        $input = $request->all();

        DB::beginTransaction();

        try {

            $save = AssociationSponsor::create([
                'user_id' => $input['user_id'],
                'association_id' => $input['association_id']
            ]);

            User::where('id', '=', $input['user_id'])->update(
                [
                    'association_id' => $input['association_id'],
                ]
            );

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'created successfully', 'association_sponsor_id' => $save->id], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['status' => 'failed', 'message' => 'create failed', 'error' => $e], 401);
        }
    }
}
