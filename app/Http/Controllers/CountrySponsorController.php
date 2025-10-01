<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CountrySponsor;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CountrySponsorController extends Controller
{
    public $successStatus = 200;

    public function listAll()
    {
        $countrySponsor = CountrySponsor::select(
            'country_sponsors.id AS id',
            'country_sponsors.user_id AS user_id',
            'country_sponsors.country_id AS country_id',
            'users.name AS name',
            'users.sponsor_type AS sponsor_type',
            'users.sponsor_tier AS sponsor_tier',
            'user_profiles.avatar AS avatar',
            'countries.name AS country_name',
            'countries.country_code AS country_code'
        )
            ->join('users', 'users.id', '=', 'country_sponsors.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->get();

        $arr = [];

        foreach ($countrySponsor as $object) {
            // if ($object['sponsor_tier'] == 1) {
            //     $arr['platinum'][] = $object->toArray();
            // } else {
            //     $arr['other'][] = $object->toArray();
            // }
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    public function listAllGlobal()
    {
        $countrySponsor = CountrySponsor::select(
            'country_sponsors.id AS id',
            'country_sponsors.user_id AS user_id',
            'country_sponsors.country_id AS country_id',
            'users.name AS name',
            'users.sponsor_type AS sponsor_type',
            'users.sponsor_tier AS sponsor_tier',
            'user_profiles.avatar AS avatar',
            'countries.name AS country_name',
            'countries.country_code AS country_code'
        )
            ->join('users', 'users.id', '=', 'country_sponsors.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->where('users.sponsor_type', 1)
            ->get();

        $arr = [];

        foreach ($countrySponsor as $object) {
            if ($object['sponsor_tier'] == 1) {
                $arr['platinum'][] = $object->toArray();
            } else {
                $arr['other'][] = $object->toArray();
            }
        }
        return response()->json($arr);
    }

    public function listAllLocal(Request $request)
    {
        $countrySponsor = CountrySponsor::select(
            'country_sponsors.id AS id',
            'country_sponsors.user_id AS user_id',
            'country_sponsors.country_id AS country_id',
            'users.name AS name',
            'users.sponsor_type AS sponsor_type',
            'users.sponsor_tier AS sponsor_tier',
            'user_profiles.avatar AS avatar',
            'countries.name AS country_name',
            'countries.country_code AS country_code'
        )
            ->join('users', 'users.id', '=', 'country_sponsors.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->whereIn('users.sponsor_type', [1, 2, 3])
            ->where('country_sponsors.country_id', '=', $request->country_id)
            ->get();

        $countrySponsorType = CountrySponsor::select(
            'country_sponsors.id AS id',
            'country_sponsors.user_id AS user_id',
            'country_sponsors.country_id AS country_id',
            'users.name AS name',
            'users.sponsor_type AS sponsor_type',
            'users.sponsor_tier AS sponsor_tier',
            'user_profiles.avatar AS avatar',
            'countries.name AS country_name',
            'countries.country_code AS country_code'
        )
            ->join('users', 'users.id', '=', 'country_sponsors.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->where('users.sponsor_type', 1)
            ->get();


        $arr = [];

        foreach ($countrySponsorType as $object) {
            if ($object['sponsor_tier'] == 1) {
                $arr['platinum'][] = $object->toArray();
            } else {
                $arr['other'][] = $object->toArray();
            }
        }

        foreach ($countrySponsor as $object) {
            if ($object['sponsor_tier'] == 1) {
                $arr['platinum'][] = $object->toArray();
            } else {
                $arr['other'][] = $object->toArray();
            }
        }

        // $tempArray = array();

        // foreach ($arr as $value) {
        //     $tempArray[serialize($value)] = $value;
        // }

        // $arr = array_values($tempArray);

        // $merged = array_merge($arr, $arr2);
        $finalPlat  = array();
        $finalOther = array();

        foreach ($arr['platinum'] as $current) {
            if (!in_array($current, $finalPlat)) {
                $finalPlat[] = $current;
            }
        }

        if(isset($arr["other"])) {
          foreach ($arr['other'] as $current) {
              if (!in_array($current, $finalOther)) {
                  $finalOther[] = $current;
              }
          }
        }

        $arr['platinum'] = $finalPlat;
        $arr['other'] = $finalOther;

        return response()->json($arr);
    }

    // protected function unique_multidimensional_array($array, $key) {
    //     $temp_array = array();
    //     $i = 0;
    //     $key_array = array();

    //     foreach($array as $val) {
    //         if (!in_array($val[$key], $key_array)) {
    //             $key_array[$i] = $val[$key];
    //             $temp_array[$i] = $val;
    //         }
    //         $i++;
    //     }
    //     return $temp_array;
    // }

    public function listAllByCountryId(Request $request)
    {
        $countrySponsor = CountrySponsor::select(
            'country_sponsors.id AS id',
            'country_sponsors.user_id AS user_id',
            'country_sponsors.country_id AS country_id',
            'users.name AS name',
            'user_profiles.avatar AS avatar',
            'countries.name AS country_name',
            'countries.country_code AS country_code'
        )
            ->join('users', 'users.id', '=', 'country_sponsors.user_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->where('country_sponsors.country_id', '=', $request->country_id)
            ->get();

        $arr = [];

        foreach ($countrySponsor as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    public function getCountryId(Request $request)
    {
        $countryId = CountrySponsor::select(
            'country_id'
        )
            ->where('user_id', '=', $request->user_id)
            ->get();

        $arr = [];

        foreach ($countryId as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    public function delete(Request $request, CountrySponsor $countrySponsor)
    {
        DB::beginTransaction();

        try {

            $country_sponsor = CountrySponsor::where('id', $request->id)->first();

            $delete = CountrySponsor::where('id', $request->id)->delete();

            $update = User::where('id', '=', $country_sponsor['user_id'])->update(
                [
                    'sponsor_type' => null,
                    'sponsor_tier' => null
                ]
            );

            DB::commit();
            // all good

            return response()->json(['status' => 'success', 'message' => 'delete success', 'country_sponsor' => $country_sponsor, '$update' => $update, '$delete' => $delete], $this->successStatus);
        } catch (\Exception $e) {

            return response()->json(['status' => 'failed', 'message' => 'delete failed', 'error' => $e], 401);

            DB::rollback();
            // something went wrong
        }

        // if ($delete) {
        //     $success = ['status' => 'success', 'message' => 'deleted successfully'];
        // } else {
        //     $success = ['status' => 'failed', 'message' => 'delete failed'];
        // }

        // return response()->json($success, $this->successStatus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'country_id' => 'required',
            'sponsor_type' => 'required',
            'sponsor_tier' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()], 401);
        }

        $input = $request->all();

        DB::beginTransaction();

        try {

            $save = CountrySponsor::create([
                'user_id' => $input['user_id'],
                'country_id' => $input['country_id']
            ]);

            $update = User::where('id', '=', $input['user_id'])->update(
                [
                    'sponsor_type' => $input['sponsor_type'],
                    'sponsor_tier' => $input['sponsor_tier']
                ]
            );

            DB::commit();
            // all good

            return response()->json(['status' => 'success', 'message' => 'created successfully', 'country_sponsor_id' => $save->id], $this->successStatus);
        } catch (\Exception $e) {

            return response()->json(['status' => 'failed', 'message' => 'create failed', 'error' => $e], 401);

            DB::rollback();
            // something went wrong
        }

        // $save = CountrySponsor::create($input);

        // if ($save) {
        //     return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        // } else {
        //     return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        // }
    }
}
