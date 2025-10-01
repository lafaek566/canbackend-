<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Country;
use App\News;
use App\Sponsor;
use App\CountrySponsor;

class CountryController extends Controller
{
    public $successStatus = 200;

    /**
     * Get all countries
     *
     * @return void
     */
    public function listAll()
    {
        $country = Country::select(
            'id',
            'name',
            'country_code'
        )
            ->where('selected', true)
            ->orderBy('id')
            ->get();

        $arr = [];

        foreach ($country as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    /**
     * Get all available country
     *
     * @return void
     */
    public function listAllAvailable()
    {
        $country = Country::select(
            'id',
            'name',
            'country_code'
        )
            ->where('selected', false)
            ->orderBy('id')
            ->get();

        $arr = [];

        foreach ($country as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    /**
     * Update selected country
     *
     * @param Country $country
     * @return void
     */
    public function updateSelected(Country $country)
    {
        $update = $country->update(
            [
                'selected' => !$country->selected
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:countries',
            'country_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()], 401);
        }

        $input = $request->all();

        $save = Country::create($input);

        if ($save) {
            return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Country $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {
        $country = Country::select('id', 'name')
            ->where('name', $request->name)
            ->get();

        $countryOld = Country::select(
            'id',
            'name',
            'country_code'
        )
            ->where('id', $request->id)
            ->get();

        $count = $country->count();

        if ($count > 0) {
            if ($countryOld[0]->id !== $country[0]->id) {
                return response()->json(['status' => 'failed', 'message' => 'country name have been used'], 200);
            } else {
                return $this->updateCountry($request);
            }
        } else {
            return $this->updateCountry($request);
        }
    }

    /**
     * Update the country
     *
     * @param Request $request
     * @return void
     */
    protected function updateCountry(Request $request)
    {
        $countryUpdate['name'] = $request->name;
        $countryUpdate['country_code'] = $request->country_code;

        $update = Country::where('id', $request->id)->update(
            [
                'name' => $countryUpdate['name'],
                'country_code' => $countryUpdate['country_code']
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    /**
     * Delete the specific resource in storage
     *
     * @param Request $request
     * @param Country $country
     * @return void
     */
    public function delete(Request $request, Country $country)
    {
        $news = News::select('id', 'country_id')
            ->where('country_id', $request->id)
            ->get();

        $countrySponsor = CountrySponsor::select('id', 'country_id')
            ->where('country_id', $request->id)
            ->get();

        $countNews = $news->count();
        $countCountrySponsor = $countrySponsor->count();

        if ($countNews > 0 || $countCountrySponsor > 0) {
            return response()->json(['status' => 'failed', 'message' => 'country have been used in news and sponsor'], 200);
        } else {
            $delete = Country::where('id', $request->id)->delete();
            if ($delete) {
                $success = ['status' => 'success', 'message' => 'deleted successfully'];
            } else {
                $success = ['status' => 'failed', 'message' => 'delete failed'];
            }

            return response()->json($success, $this->successStatus);
        }
    }
}
