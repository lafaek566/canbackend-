<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\UserProfile;
use App\User;
use App\ClassGrade;
use App\CompetitionActivity;
use App\EventMemberClass;

class UserProfileController extends Controller
{
    public $successStatus = 200;

    public function listDetail(Request $request)
    {
        $userProfile = User::select(
            'users.id AS id',
            'users.name',
            'email',
            'sponsor_type',
            'role_id',
            'roles.role_name AS role_name',
            'sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'country_id',
            'countries.name AS country_name',
            'association_id',
            'associations.name AS associations_name',
            'user_profiles.avatar AS avatar',
            'user_profiles.banner AS banner',
            'user_profiles.biography AS biography',
            'user_profiles.phone_no AS phone_no',
            'user_profiles.phone_verified_at AS phone_verified_at'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
            ->leftJoin("associations", "associations.id", "=", "users.association_id")
            ->where('users.id', '=', $request->user_id)
            ->get();

        return response()->json($userProfile);
    }

    public function listDetailStats(Request $request)
    {
        $userPointAndRating = User::select(
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'judge_rating',
            'member_rating',
            'role_id',
            \DB::raw('(SELECT COUNT(*) FROM cars WHERE cars.user_id = ' . $request->user_id . ') AS cars_count')
        )
            ->where('users.id', '=', $request->user_id)
            ->first();

        if (!$userPointAndRating) {
            return response()->json([
                'error' => true,
                'message' => 'User not found'
            ], 404);
        }

        $points[] = $userPointAndRating['can_q_consumer_point'];
        $points[] = $userPointAndRating['can_q_prosumer_point'];
        $points[] = $userPointAndRating['can_q_professional_point'];

        $class_grades = ClassGrade::select('name')->take(3)->get()->toArray();

        $ranges = new \stdClass();
        $count = new \stdClass();

        for ($i = 0; $i < sizeof($class_grades); $i++) {
            $ranges->$i = $class_grades[$i]['name'];
            $count->$i = (!$points[$i] ? 0 : $points[$i]);
        }

        $can_q_points = [
            'ranges' => $ranges,
            'currentRange' => '0',
            'data' => [
                'label' => 'CAN Q POINTS',
                'count' => $count
            ],
            'color' => 'light-blue-fg',
            'detail' => "For CAN Q, only 16 high-ranking contestants will be invited, from the results of the year's CAN Q match (16 participant for each class-grade)."
        ];

        $activities = CompetitionActivity::select('id', 'name')->get()->toArray();

        $ranges = new \stdClass();
        $count = new \stdClass();

        for ($i = 0; $i < sizeof($activities); $i++) {
            $ranges->$i = $activities[$i]['name'];

            $eventMemberClass = EventMemberClass::select(
                'event_member_id',
                'competition_activity_id'
            )
                ->leftJoin('event_members', 'event_members.id', '=', 'event_member_id')
                ->leftJoin('events', 'events.id', '=', 'event_members.event_id')
                ->leftJoin('users', 'users.id', '=', 'event_members.member_id')
                ->where('competition_activity_id', '=', $activities[$i]['id'])
                ->where('users.id', '=', $request->user_id)
                ->where('events.status_score_final', '=', 1)
                ->count();

            $count->$i = $eventMemberClass;
            $count2[] = $eventMemberClass;
        }

        $activitiesStats = [
            'ranges' => $ranges,
            'currentRange' => '0',
            'data' => [
                'label' => 'TOTAL ENTRIES',
                'count' => $count2
            ],
            'color' => 'blue-grey-fg',
            'detail' => "This is the total amount of activity classes you have participated."
        ];

        $cars = [
            'title' => 'Cars',
            'currentRange' => null,
            'data' => [
                'label' => 'AMOUNT',
                'count' => ($userPointAndRating['cars_count'] == null ? 0 : $userPointAndRating['cars_count'])
            ],
            'color' => 'red-fg',
            'detail' => "This is the total amount of cars you have setup on your dashboard."
        ];

        $count = ($userPointAndRating['role_id'] == 6 ? $userPointAndRating['member_rating'] : $userPointAndRating['judge_rating']);
        $count = ($count == null ? 0 : $count);

        $rating = [
            'title' => 'Rating',
            'currentRange' => null,
            'data' => [
                'label' => 'AVERAGE',
                'count' => $count
            ],
            'color' => 'orange-fg',
            'detail' => "This is the average rating from all the event you had participated in. Given from the judge who assessed you ."
        ];

        $can_q_stats[] = $can_q_points;
        $can_q_stats[] = $activitiesStats;
        $can_q_stats[] = $cars;
        $can_q_stats[] = $rating;

        return response()->json($can_q_stats);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userProfile = UserProfile::where('user_id', $request->user_id)->first();

        $uploadPath = public_path('upload/');
        $storagePath = public_path('upload/files/');
        $userProfilePath = public_path('upload/files/user-profile/');

        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');
        $random = str_random(50);
        $title = $random . $waktu;

        if ($userProfile) {
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath);
            }

            if (!file_exists($storagePath)) {
                mkdir($storagePath);
            }

            if (!file_exists($userProfilePath)) {
                mkdir($userProfilePath);
            }

            if (!file_exists($userProfilePath . $request->user_id . '/')) {
                mkdir($userProfilePath . $request->user_id . '/');
            }

            $cekAvatar = substr($request->avatar, 0, 10);

            if ($userProfile->avatar !== null && $userProfile->avatar !== '') {
                $path = parse_url($userProfile->avatar, PHP_URL_PATH);
                $file_name = basename($path);
                $fileAvatarLink = $userProfilePath .  $request->user_id . '/' . $file_name;

                if (file_exists($fileAvatarLink)) {
                    unlink($fileAvatarLink);
                }
            }

            if ($cekAvatar === 'data:image') {
                $image_parts = explode(";base64,", $request->avatar);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileAvatarLink = $userProfilePath . $request->user_id . '/' . $title . '.' . $image_type;
                $fileAvatarUrl = $protocol . '://' . $domain . '/upload/files/user-profile/' . $request->user_id . '/' . $title . '.' . $image_type;
                file_put_contents($fileAvatarLink, $image_base64);
            } else {
                $fileAvatarUrl = null;
            }

            $userProfileUpdate['avatar'] = $fileAvatarUrl;

            $update = UserProfile::where('user_id', $request->user_id)->update(
                [
                    'avatar' => $userProfileUpdate['avatar']
                ]
            );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 401);
        }
    }

    public function updateBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userProfile = UserProfile::where('user_id', $request->user_id)->first();

        $uploadPath = public_path('upload/');
        $storagePath = public_path('upload/files/');
        $userProfilePath = public_path('upload/files/user-profile/');

        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');
        $random = str_random(50);
        $title = $random . $waktu;

        if ($userProfile) {
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath);
            }

            if (!file_exists($storagePath)) {
                mkdir($storagePath);
            }

            if (!file_exists($userProfilePath)) {
                mkdir($userProfilePath);
            }

            if (!file_exists($userProfilePath . $request->user_id . '/')) {
                mkdir($userProfilePath . $request->user_id . '/');
            }

            $cekBanner = substr($request->banner, 0, 10);

            if ($userProfile->banner !== null && $userProfile->banner !== '') {
                $path = parse_url($userProfile->banner, PHP_URL_PATH);
                $file_name = basename($path);
                $fileBannerLink = $userProfilePath .  $request->user_id . '/' . $file_name;

                if (file_exists($fileBannerLink)) {
                    unlink($fileBannerLink);
                }
            }

            if ($cekBanner === 'data:image') {
                $image_parts = explode(";base64,", $request->banner);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileBannerLink = $userProfilePath . $request->user_id . '/' . $title . '.' . $image_type;
                $fileBannerUrl = $protocol . '://' . $domain . '/upload/files/user-profile/' . $request->user_id . '/' . $title . '.' . $image_type;
                file_put_contents($fileBannerLink, $image_base64);
            } else {
                $fileBannerUrl = null;
            }

            $userProfileUpdate['banner'] = $fileBannerUrl;

            $update = UserProfile::where('user_id', $request->user_id)->update(
                [
                    'banner' => $userProfileUpdate['banner']
                ]
            );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 401);
        }
    }

    public function updateBiography(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'biography' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userProfile = UserProfile::where('user_id', $request->user_id)->first();

        if ($userProfile) {
            $userProfileUpdate['biography'] = $request->biography;

            $update = UserProfile::where('user_id', $request->user_id)->update(
                [
                    'biography' => $userProfileUpdate['biography']
                ]
            );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'updated successfully', 'data' => $userProfileUpdate['biography']], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 401);
        }
    }

    public function updateUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            $userUpdate['name'] = $request->name;

            $update = User::where('id', $request->user_id)->update(
                [
                    'name' => $userUpdate['name']
                ]
            );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 401);
        }
    }

    public function updatePhoneNo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_no' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            $userUpdate['phone_no'] = $request->phone_no;

            $update = UserProfile::where('user_id', $request->user_id)->update(
                [
                    'phone_no' => $userUpdate['phone_no']
                ]
            );

            if ($update) {
                return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 401);
        }
    }
}
