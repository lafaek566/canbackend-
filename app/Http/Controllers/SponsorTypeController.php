<?php

namespace App\Http\Controllers;

use App\SponsorType;
use Illuminate\Http\Request;

class SponsorTypeController extends Controller
{
    public function listAll(Request $request)
    {
        $types = SponsorType::select(
            'id',
            'name'
        )
            ->orderBy('id')
            ->get();

        $arr = [];

        foreach ($types as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }
}
