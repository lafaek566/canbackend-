<?php

namespace App\Http\Controllers\Auth;

use App\AssociationSponsor;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\User;
use App\UserProfile;
use App\Mail\ConfirmationMail;
use App\Mail\ResetPasswordMail;
use App\Car;
use App\EventMemberResult;
use App\EventJudgeResult;
use App\News;
use App\EventMember;
use App\EventJudge;
use App\CountrySponsor;
use App\Link;
use Illuminate\Support\Facades\Schema;
use App\EventSponsor;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Psr\Http\Message\ResponseInterface;

class ApiRegisterController extends Controller
{

    public $successStatus = 200;


    public function generateConfirmCode()
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < 6) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    public function test()
    {
        $confirmCode = $this->generateConfirmCode();
        return response()->json($confirmCode);
    }

    public function sendMail($confirmCode, $receiver)
    {
        $objDemo = new \stdClass();
        $objDemo->sender = 'CAN Organizer';
        $objDemo->confirmCode = $confirmCode;

        Mail::to($receiver)->send(new ConfirmationMail($objDemo));
    }

    public function sendResetPasswordMail($token, $receiver, $time)
    {
        $objDemo = new \stdClass();
        $domain = \Config::get('project-config.project_link');
        $dataLink = $domain . '/#/auth/password-reset/' . $receiver->id . '/' . $token;
        $objDemo->sender = 'CAN Organizer';
        $objDemo->receiverName = $receiver->name;
        $objDemo->receiverMail = $receiver->email;
        $objDemo->link = $dataLink;
        $objDemo->time = $time;

        Mail::to($receiver)->send(new ResetPasswordMail($objDemo));
    }

    /**
     * Handle a registration request for the application.
     *
     * @override
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $message = array(
            'name.required' => 'Name are required',
            'email.required' => 'email are required',
            'password.required' => 'password are required'
        );

        $valid = validator($request->only('email', 'name', 'password'), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:8'
        ]);

        if ($valid->fails()) {
            $jsonError = response()->json($valid->errors()->all(), 400);
            return \Response::json($jsonError);
        }

        $AlreadyUser = User::where('email', $request->email)->first();

        if ($AlreadyUser !== null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'email have been used'
            ]);
        }

        $data = request()->only('email', 'name', 'password');
        $confirmCode = $this->generateConfirmCode();
        //$data = $tempAccount->only('email','name','password', 'slug');
        event(new Registered($user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'email_confirm_code' => $confirmCode,
            'password' => Hash::make($request['password']),
            'role_id' => 4,
        ])));

        $userProfile = UserProfile::create([
            'user_id' => $user->id,
        ]);

        $this->sendMail($confirmCode, $request['email']);

        $client = DB::table('oauth_clients')->where('password_client', 1)->first();

        $request->request->add([
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $data['email'],
            'password'      => $data['password'],
            'scope'         => null
        ]);
        // Fire off the internal request.
        $token = Request::create(
            'oauth/token',
            'POST'
        );
        return \Route::dispatch($token);
    }

    public function resendConfirmCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('email', $request->email)->first();

        $confirmCode = $this->generateConfirmCode();

        if ($user) {
            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }
            $user->email_confirm_code = $confirmCode;
            $user->save();
        } else {
            return response()->json(['status' => 'failed', 'message' => 'email not registered'], 200);
        }

        $this->sendMail($confirmCode, $request->email);

        return response()->json(['status' => 'success', 'message' => 'confirm code have been resend'], 200);
    }

    public function sendSMSCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'phone_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // $confirmCode = $this->generateConfirmCode();

        $userProfile = UserProfile::where('phone_no', $request->phone_no)->first();

        if ($userProfile) {
            $phoneVerifiedAt = $userProfile->phone_verified_at;

            if ($phoneVerifiedAt) {
                return response()->json(['status' => 'failed', 'message' => 'phone already used'], 200);
            }
        }

        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }

            $userProfile = UserProfile::where('user_id', $request->user_id)->first();

            if ($userProfile) {
                // if (substr($request->phone_no, 0, 2) == '65') {
                // $userProfile->phone_no = $request->phone_no;
                // $userProfile->save();
                // return response()->json(['status' => 'success', 'message' => 'verification has been sent'], 200);
                // } else {
                $client = new Client();
                $res = $client->post('https://api.nexmo.com/v2/verify', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MjM2MjkyMDMsImV4cCI6MjU4NzU0MjgwMywianRpIjoiUHI0QUJvaUdteFRKIiwiYXBwbGljYXRpb25faWQiOiJlNzdjYjg4MC02YWQ0LTQ0YmMtOTMxZi1kZDg5ZDY0ZWQyMWMiLCJzdWIiOiIiLCJhY2wiOiIifQ.Aagc4tWt3imdCVAYWOtglnJze-OIF0JDQg_RENDmgnYl5dK_MZXXm0MY_gHHEm2SpVENUdNwO7baxBYKZlk-db6iEGOV18TNtY9ZTZdqL6p9DnGuCaGc09RTk89usM6A5IzTVuLo0jVh7EMH6h98Tf_pRUoIA1CQw1HlgUkdoYBRicCySiTVcv8fbBWaiWklWNniaDhIpVCpok5sxSUOi0TGBZ3zpz059AQKOSxLePa8sCwZIx4RlUAizcsQdiIkL5B71zzv_70HfKkFhy1Db7cVTYi3lW9F0LcJzytjanmg9CcXKqXw5uEAMuuhm8m8xwSPym-cnJBC469dPZcL_A',
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'brand' => 'CAN',
                        'workflow' => [
                            [
                                'channel' => 'whatsapp',
                                'to' => $request->phone_no,
                                'from' => '6281211564088'
                            ],
                            [
                                'channel' => 'sms',
                                'to' => $request->phone_no,
                            ]
                        ]
                    ],
                ]);

                if ($res->getStatusCode() == 202) {
                    $body = json_decode($res->getBody());

                    $userProfile->phone_request_id = $body->request_id;
                    $userProfile->save();

                    return response()->json(['status' => 'success', 'message' => 'verification has been sent', 'body' => $body, 'res2' => $res->getStatusCode()], 200);
                } else if ($res->getStatusCode() == 429) {
                    return response()->json(['status' => 'failed', 'message' => 'please try again some later time, too many attempts'], 200);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'verification failed to send'], 401);
                }
                // }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'user profile not found'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }

        // $this->sendMail($confirmCode, $request->email);

        // return response()->json(['status' => 'success', 'message' => 'a new verification code has been sent'], 200);
    }

    public function sentResetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }

            $token = str_random(60);
            $domain = \Config::get('project-config.project_link');
            $dataLink = $domain . '/auth/password-reset/' . $user->id . '/' . $token;
            $time = date('Y-m-d H:i:s', strtotime('+2 hours'));

            $input['link'] = $dataLink;
            $input['expired_time'] = $time;
            $input['status_expired'] = 0;

            $saveLink = Link::create($input);
            $this->sendResetPasswordMail($token, $user, $time);
            return response()->json(['status' => 'success', 'message' => 'email have been sent'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'email not registered'], 200);
        }
    }

    public function checkResetPasswordLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $link = Link::where('link', $request->link)->first();

        if ($link) {
            $time = date('Y-m-d H:i:s');
            $expired_time = $link->expired_time;
            if ($time > $expired_time) {
                return response()->json(['status' => 'success', 'expired' => true], 200);
            } else {
                return response()->json(['status' => 'success', 'expired' => false], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'link not found'], 200);
        }
    }

    public function resetPassword(Request $request, User $user)
    {
        $user = User::where('id', $request->id)->first();
        $link = Link::where('link', $request->link)->first();

        if ($user) {
            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }

            $time = date('Y-m-d H:i:s');

            $user->password = Hash::make($request->password);
            $user->save();

            if ($link) {
                $link->expired_time = $time;
                $link->save();
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }

        return response()->json(['status' => 'success', 'message' => 'password have been updated successfully'], 200);
    }

    public function changeEmail(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userEmail = User::where('email', $request->email)->first();

        if ($userEmail) {
            return response()->json(['status' => 'failed', 'message' => 'this email has been used'], 200);
        }

        $user = User::where('id', $request->user_id)->first();
        $confirmCode = $this->generateConfirmCode();

        if ($user) {
            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }

            if ($user->email === $request->email) {
                return response()->json(['status' => 'failed', 'message' => 'same email'], 200);
            }

            $user->email_confirm_code = $confirmCode;
            $user->save();

            $this->sendMail($confirmCode, $request->email);

            return response()->json(['status' => 'success', 'message' => 'code has been sent'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }
    }


    public function changeEmailVerify(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'verification_code' => 'required',
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->user_id)->first();

        if ($user) {

            $emailConfirmCode =  $user->email_confirm_code;

            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }

            if ($user->email === $request->email) {
                return response()->json(['status' => 'failed', 'message' => 'same email'], 200);
            }

            if ($emailConfirmCode !== $request->verification_code) {
                return response()->json(['status' => 'failed', 'message' => 'wrong confirm code'], 200);
            }

            $dateVerified = date('Y-m-d H:i:s');

            $user->email = $request->email;
            $user->email_verified_at = $dateVerified;
            $user->save();

            return response()->json(['status' => 'success', 'message' => 'email have been updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }
    }

    public function checkConfirmStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->id)->first();

        if ($user) {
            if ($user->email_verified_at == null) {
                $verified = false;
            } else {
                $verified = true;
            }

            $userProfile = UserProfile::where('user_id', $request->id)->first();

            if ($userProfile->phone_verified_at != null || ($user->grouped_user_id != null && $userProfile->phone_no != null)) {
                $phone_verified = true;
            } else {
                $phone_verified = false;
            }

            $user['phone'] = $userProfile->phone_no;
            $user['phone_verified_at'] = $userProfile->phone_verified_at;
            $user['biography'] = $userProfile->biography;
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }

        return response()->json(['status' => 'success', 'verified' => $verified, 'phone_verified' => $phone_verified, 'user' => $user], 200);
    }

    public function checkBannedStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->id)->first();

        if ($user) {
            if ($user->status_banned === 1) {
                $banned = true;
            } else {
                $banned = false;
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }

        return response()->json(['status' => 'success', 'banned' => $banned], 200);
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'email_confirm_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('email', $request->email)->first();
        $emailVerifiedAt = $user->email_verified_at;
        $emailConfirmCode =  $user->email_confirm_code;

        if ($user->status_banned === 1) {
            return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
        }

        // return response()->json(['status' => 'success', 'emailConfirmCode' => $emailConfirmCode, 'email_confirm_code' => $request->email_confirm_code], 200);

        $dateVerified = date('Y-m-d H:i:s');

        if ($user) {
            if ($emailVerifiedAt) {
                return response()->json(['status' => 'failed', 'message' => 'email already verified'], 200);
            }
            if ($emailConfirmCode !== $request->email_confirm_code) {
                return response()->json(['status' => 'failed', 'message' => 'wrong confirm code'], 200);
            }
            $user->email_verified_at = $dateVerified;
            $user->save();
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }

        return response()->json(['status' => 'success', 'email_verified_at' => $dateVerified], 200);
    }


    public function verifySMSCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'phone_no' => 'required',
            'sms_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userProfile = UserProfile::where('phone_no', $request->phone_no)->first();

        if ($userProfile) {
            $phoneVerifiedAt = $userProfile->phone_verified_at;

            if ($phoneVerifiedAt) {
                return response()->json(['status' => 'failed', 'message' => 'phone already used'], 200);
            }
        }


        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }
        }

        $userProfile = UserProfile::where('user_id', $request->user_id)->first();

        $dateVerified = date('Y-m-d H:i:s');

        if ($userProfile) {

            $phoneRequestId =  $userProfile->phone_request_id;
            $smsCode =  $request->sms_code;

            $client = new Client();
            $res = $client->post('https://api.nexmo.com/v2/verify/' . $phoneRequestId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MjM2MjkyMDMsImV4cCI6MjU4NzU0MjgwMywianRpIjoiUHI0QUJvaUdteFRKIiwiYXBwbGljYXRpb25faWQiOiJlNzdjYjg4MC02YWQ0LTQ0YmMtOTMxZi1kZDg5ZDY0ZWQyMWMiLCJzdWIiOiIiLCJhY2wiOiIifQ.Aagc4tWt3imdCVAYWOtglnJze-OIF0JDQg_RENDmgnYl5dK_MZXXm0MY_gHHEm2SpVENUdNwO7baxBYKZlk-db6iEGOV18TNtY9ZTZdqL6p9DnGuCaGc09RTk89usM6A5IzTVuLo0jVh7EMH6h98Tf_pRUoIA1CQw1HlgUkdoYBRicCySiTVcv8fbBWaiWklWNniaDhIpVCpok5sxSUOi0TGBZ3zpz059AQKOSxLePa8sCwZIx4RlUAizcsQdiIkL5B71zzv_70HfKkFhy1Db7cVTYi3lW9F0LcJzytjanmg9CcXKqXw5uEAMuuhm8m8xwSPym-cnJBC469dPZcL_A',
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'code' => $smsCode
                ],
            ]);

            $body = json_decode($res->getBody());

            if ($res->getStatusCode() == 200) {
                $userProfile->phone_no = $request->phone_no;
                $userProfile->phone_verified_at = $dateVerified;
                $userProfile->save();

                return response()->json(['status' => 'success', 'message' => 'phone successfuly verified!', 'phone_verified_at' => $dateVerified], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => $body->detail], 200);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }

        return response()->json(['status' => 'success', 'phone_verified_at' => $dateVerified], 200);
    }

    public function changeRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->id)->first();

        $userProfile = UserProfile::where('user_id', $request->id)->first();

        $saveCountrySponsor['id'] = null;

        if ($user) {

            if ($user->status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'this account have been banned'], 200);
            }

            $role_id = (int) $user->role_id;

            if ($role_id !== 4) {
                return response()->json(['status' => 'failed', 'message' => 'failed change user role because user was not CAN Friend'], 200);
            }

            if ($userProfile->phone_verified_at === null) {
                return response()->json([
                    'status' => 'failed',
                    'message' => "This user's cell phone number is unverified. Contact them or use 'Force Verify Number'."
                ]);
            }

            $userProfile = UserProfile::where('user_id', $user->id)->first();

            $user->role_id = (int) $request->role_id;

            // SET NORMAL SPONSOR
            if ($request->sponsor_type !== null && $request->sponsor_type !== '' && $request->sponsor_tier !== null && $request->sponsor_tier !== '') {
                $user->sponsor_type = (int) $request->sponsor_type;
                $user->sponsor_tier = (int) $request->sponsor_tier;
                $user->country_id = (int) $request->country_id;
                $user->save();

                $countrySponsor['user_id'] = $request->id;
                $countrySponsor['country_id'] = $request->country_id;

                $saveCountrySponsor = CountrySponsor::create($countrySponsor);
            } else if ($request->association_id !== null && $request->role_id == 3) { // SET ASSOCIATION SPONSOR
                $user->association_id = (int) $request->association_id;
                $user->save();

                $associationSponsor['user_id'] = $request->id;
                $associationSponsor['association_id'] = $request->association_id;

                AssociationSponsor::create($associationSponsor);
            } else if ($request->sponsor_type !== null && $request->role_id == 8) { // SET SPONSOR ADMIN
                $user->sponsor_type = (int) $request->sponsor_type;
                if ($request->sponsor_type == 1) {
                    $user->country_id = null;
                } else {
                    $user->country_id = (int) $request->country_id;
                }
                $user->save();
            } else if ($request->country_id != null && $request->role_id == 10) { // SET COUNTRY ADMIN
                $user->sponsor_type = 2; // Make it local
                $user->country_id = (int) $request->country_id;
                $user->save();
            } else if ($request->association_id != null && $request->role_id == 11) { // SET ASSOCIATION ADMIN
                $user->association_id = (int) $request->association_id;
                $user->save();
            } else {
                $user->sponsor_type = null;
                $user->sponsor_tier = null;
                $user->country_id = null;
                $user->save();
            }

            $userProfile->save();

            return response()->json(['status' => 'success', 'message' => 'user role have been updated successfully', '$request->sponsor_type' => $request->sponsor_type], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }
    }

    public function delete(Request $request, User $user)
    {
        $userProfile = UserProfile::select('id', 'user_id', 'avatar')
            ->where('user_id', $request->id)
            ->get();

        $eventMember = EventMember::select('id', 'member_id')
            ->where('member_id', $request->id)
            ->get();

        $eventSponsor = EventSponsor::select('id', 'sponsor_id')
            ->where('sponsor_id', $request->id)
            ->get();

        // $eventMemberResult = EventMemberResult::select('id', 'judge_id')
        //     ->where('judge_id', $request->id)
        //     ->get();

        $eventJudge = EventJudge::select('id', 'judge_id')
            ->where('judge_id', $request->id)
            ->get();

        // $eventJudgeResult = EventJudgeResult::select('id', 'member_id')
        //     ->where('member_id', $request->id)
        //     ->get();

        $news = News::select('id', 'user_id')
            ->where('user_id', $request->id)
            ->get();

        $countUserProfile = $userProfile->count();
        // $countCar = $car->count();
        $countEventMember = $eventMember->count();
        $countEventSponsor = $eventSponsor->count();
        // $countEventMemberResult = $eventMemberResult->count();
        $countEventJudge = $eventJudge->count();
        // $countEventJudgeResult = $eventJudgeResult->count();
        $countNews = $news->count();

        if (
            /* $countCar > 0 || */
            $countEventMember > 0
            || $countEventJudge > 0 || $countNews > 0 || $countEventSponsor > 0
        ) {
            return response()->json(['status' => 'failed', 'message' => 'user have been used in other table'], 200);
        } else {
            Schema::disableForeignKeyConstraints();

            // Delete all cars
            $delete = Car::where('user_id', $request->id)->delete();
            if ($delete) {
            } else {
                $success = ['status' => 'failed', 'message' => 'delete user cars failed'];
            }

            $delete = UserProfile::where('user_id', $request->id)->delete();
            if ($delete) {
                $delete = User::where('id', $request->id)->delete();
                if ($delete) {
                    $success = ['status' => 'success', 'message' => 'deleted successfully'];
                } else {
                    $success = ['status' => 'failed', 'message' => 'delete failed'];
                }
            } else {
                $success = ['status' => 'failed', 'message' => 'delete failed'];
            }

            Schema::enableForeignKeyConstraints();

            return response()->json($success, $this->successStatus);
        }
    }
    /*
    public function registerByInvites(Request $request)
    {
        $message = array(
            'name.required' =>'Name are required',
            'email.required' =>'email are required',
            'slug.required' =>'url address are required',
            'password.required' =>'password are required',
        );

        $valid = validator($request->only('email', 'name', 'password','slug'), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'slug' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            $jsonError=response()->json($valid->errors()->all(), 400);
            return \Response::json($jsonError);
        }

        $AlreadyUser = User::where('email',$request->email)->where('slug',$request->slug)->first();

        if($AlreadyUser !== null)
        {
            return response()->json([
                'status' => 'failed',
                'data' =>$AlreadyUser->toArray(),
                'extra' =>'user ini sudah pernah buat account',
            ]);
        }
*/

    /**
     * START CREATE USER
     */
    /*
         $data = request()->only('email','name','password', 'slug');
        //$data = $tempAccount->only('email','name','password', 'slug');
        event(new Registered($user = User::create([
            'slug' => $request['slug'],
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role_id' => $request['role_id'],
        ])));

        $foUserProfile = FoUserProfile::create([
            'user_id' => $user->id,
        ]);

*/
    /**
     * CLEAR INVITE TABLE OF THIS USER
     */
    //      $foInvite = FoInvite::where('id',$request->invite_id)->first();
    //     $foInvite->delete();


    /**
     * SEND TOKEN TO USER
     */
    /*
        $client = DB::table('oauth_clients')->where('password_client',1)->first();
        $request->request->add([
            'grant_type'    => 'password',
            'client_id'     => $client->id,
            'client_secret' => $client->secret,
            'username'      => $request['email'] . '#*+*#' . $request['slug'],
            'password'      => $request['password'],
            'scope'         => null,
        ]);
        // Fire off the internal request.
        $token = Request::create(
            'oauth/token',
            'POST'
        );
        return \Route::dispatch($token);

    }
*/
    /**
     * FOR TEMP ACCOUNT
     */
    // public function createTemp(Request $request)
    // {
    //     $code = mt_rand(100000, 999999);
    //     $tempAccount = $request;
    //     $tempAccount['code'] = $code;

    //     //send email
    //     $objDemo = new \stdClass();

    //     $objDemo->sender = 'Fixle Planner';
    //     $objDemo->code = $code;
    //     Mail::to($tempAccount->email)->send(new ConfirmationMail($objDemo));

    //     $temp = TempAccount::create($tempAccount->all());
    //     $temp->code = '';
    //     return response()->json($temp, 201);
    // }

    public function confirmCode(Request $request)
    {
        $tempAccount = TempAccount::where('id', $request->id)->where('code', $request->code)->first();
        \Debugbar::info($tempAccount);
        if ($tempAccount) {
            return response()->json([
                'status' => 'success',
                'data' => 'success',
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'data' => 'failed',
        ]);
    }

    public function update(Request $request, TempAccount $tempAccount)
    {
        $valid = validator($request->only('email', 'name', 'password', 'slug'), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'slug' => 'required|unique:fo_accounts',
            'password' => 'required|string|min:6',
        ]);

        if ($valid->fails()) {
            $jsonError = response()->json($valid->errors()->all(), 400);
            return \Response::json($jsonError);
        }

        $request['verify'] = mt_rand(100000, 999999);

        $tempAccount->update($request->all());

        return response()->json($tempAccount, 200);
    }
}
