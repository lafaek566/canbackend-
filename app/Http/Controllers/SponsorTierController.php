<?php

namespace App\Http\Controllers;

use App\SponsorTier;
use Illuminate\Http\Request;

class SponsorTierController extends Controller
{
    public function listAll(Request $request)
    {
        $tier = SponsorTier::select(
            'id',
            'name'
        )
            ->orderBy('id')
            ->get();

        $arr = [];

        foreach ($tier as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }
}
