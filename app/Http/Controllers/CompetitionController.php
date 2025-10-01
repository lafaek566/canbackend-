<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;

use App\Competition;
use App\EventMember;
use App\EventJudge;

class CompetitionController extends Controller
{
    public $successStatus = 200;


    public function listAll(Request $request)
    {
        $competition = Competition::select(
            'id',
            'title',
            'subtitle',
            'banner',
            'description',
            'type',
            'updated_at',
            'created_at'
        )
            ->orderBy('id')
            ->get();

        $arr = [];

        foreach ($competition as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

}
