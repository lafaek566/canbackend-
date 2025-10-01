<?php

namespace App\Http\Controllers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\User\UserResource;

class FirstLoadController extends Controller
{


    public function load(Request $request)
    {    
        
        $user = Auth::user();
        
        $userDetail = $user;
        $userDetail = $userDetail->load('userProfile');
        // $userDetail = $userDetail->load('userOtherSettings');
        // $foAccount = FoAccount::load('account_team')->where('name',$request->accountName)->first();
        return response()->json([
            'status' => 'success',
            'data' => UserResource::make($userDetail),
        ]);

    }

}
