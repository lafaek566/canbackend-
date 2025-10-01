<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeController extends Controller
{
    public $successStatus = 200;

    public function getTimeStamp() {
        $date = date('Y-m-d H:i:s');

        return response()->json(['time' => $date], 200);
    }
}
