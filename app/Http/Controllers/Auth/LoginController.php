<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\UserProfile;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            // 1. Dapatkan objek user otentikasi
            /** @var \App\User $user */ // Type hint untuk Intelephense
            $user = $this->guard()->user(); 
            
            // 2. Muat relasi userProfile (Eager Load) untuk mencegah error 'non-object'
            $user->load('userProfile'); 
            
            // 3. Buat Access Token Passport
            $token = $user->createToken('MyApp')->accessToken;

            // 4. Tambahkan data userProfile ke objek user
            // Baris ini (atau baris 63 di error lama) adalah yang menyebabkan masalah
            if ($user->userProfile) { 
                $user->avatar = $user->userProfile->avatar;
                $user->banner = $user->userProfile->banner;
                $user->phone = $user->userProfile->phone_no;
                $user->phone_verified_at = $user->userProfile->phone_verified_at;
                $user->biography = $user->userProfile->biography;
            } else {
                $user->avatar = null;
                $user->banner = null;
                $user->phone = null;
                $user->biography = null;
            }

            // 5. Return response sukses
            return response()->json([
                'status' => 'success',
                'data' => $user->toArray(),
                'token' => $token,
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [ 'status' => 'error','error' => trans('auth.failed') ];
        return response()->json($errors);
    }

    public function logout(Request $request)
    {
        if (Auth::user()) {
            /** @var \App\User $authUser */
            $authUser = Auth::user();
            $authUser->token()->revoke();
        }

        $user = User::where('id', $request->id)->first();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['status' => 'success', 'message' => 'logged out successfully'], 200);
    }

    public function switchUser(Request $request) 
    {
        return response()->json(['status' => 'success', 'message' => 'Switch user functionality not implemented yet.']);
    }

}