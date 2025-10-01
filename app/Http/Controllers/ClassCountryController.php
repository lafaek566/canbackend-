<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClassGrade;
use App\ClassCountry;

class ClassCountryController extends Controller
{
    public $successStatus = 200;

    public function listAll()
    {
        $classCountry = ClassCountry::select(
            'id',
            'name'
        )
            ->get();

        $arr = [];

        foreach ($classCountry as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr], 200);
    }
}
