<?php

namespace App\Http\Controllers;

use App\AssociationSponsor;
use Illuminate\Http\Request;
use App\User;
use App\CountrySponsor;
use App\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    public function listAll(Request $request)
    {
        $user = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'user_profiles.phone_no AS phone_no',
            'user_profiles.phone_verified_at AS phone_verified_at',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'users.country_id AS user_country_id',
            'c2.name AS user_country_name',
            'users.association_id AS user_association_id',
            'associations.name AS user_association_name',
            'country_sponsors.id AS country_id',
            // 'countries.name AS country_sponsor_name',
            'c1.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries AS c1', 'c1.id', '=', 'country_sponsors.country_id')
            ->leftJoin('countries AS c2', 'c2.id', '=', 'users.country_id')
            ->leftJoin('associations', 'associations.id', '=', 'users.association_id')
            ->where('email_verified_at', '<>', null)
            ->where(function ($query) {
                $query->where(DB::raw('substr(user_profiles.phone_no, 1, 2)'), '!=', '00')
                    ->orWhere('user_profiles.phone_verified_at', '<>', null)
                    ->orWhereNull('user_profiles.phone_no');
            })
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('roles.role_name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column ?? 'users.created_at', $request->sort ?? 'desc')
            ->get();

        $userCount = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            // 'users.country_id AS country_id',
            'country_sponsors.id AS country_id',
            // 'countries.name AS country_sponsor_name',
            'countries.name AS sponsor_country_name',
            "association_sponsors.id AS association_id",
            "associations.name AS sponsor_association_name",
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->leftJoin('association_sponsors', 'association_sponsors.user_id', '=', 'users.id')
            ->leftJoin('associations', 'associations.id', '=', 'association_sponsors.association_id')
            ->where('email_verified_at', '<>', null)
            ->where(function ($query) {
                $query->where(DB::raw('substr(user_profiles.phone_no, 1, 2)'), '!=', '00')
                    ->orWhere('user_profiles.phone_verified_at', '<>', null)
                    ->orWhereNull('user_profiles.phone_no');
            })
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('roles.role_name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $userCount]);
    }

    public function listVerify(Request $request)
    {
        $user = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'user_profiles.phone_no AS phone_no',
            'user_profiles.phone_verified_at AS phone_verified_at',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'users.country_id AS user_country_id',
            'c2.name AS user_country_name',
            'country_sponsors.id AS country_id',
            // 'countries.name AS country_sponsor_name',
            'c1.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries AS c1', 'c1.id', '=', 'country_sponsors.country_id')
            ->leftJoin('countries AS c2', 'c2.id', '=', 'users.country_id')
            ->where('email_verified_at', '<>', null)
            ->where('phone_verified_at', '=', null)
            ->where(DB::raw('substr(user_profiles.phone_no, 1, 2)'), '=', '00')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('roles.role_name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $userCount = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            // 'users.country_id AS country_id',
            'country_sponsors.id AS country_id',
            // 'countries.name AS country_sponsor_name',
            'countries.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->where('email_verified_at', '<>', null)
            ->where('phone_verified_at', '=', null)
            ->where(DB::raw('substr(user_profiles.phone_no, 1, 2)'), '=', '00')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('roles.role_name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        // for ($i = 0; $i < sizeof($arr); $i++) {
        //     $country_sponsor = $this->getCountryIdByUserId($arr[$i]['id']);
        //     if ($country_sponsor !== null) {
        //         $arr[$i]['country_id'] = $country_sponsor->country_id;
        //     } else {
        //         $arr[$i]['country_id'] = $country_sponsor;
        //     }
        // }
        return response()->json(['data' => $arr, 'total' => $userCount]);
    }

    public function listAllTopSixteen(Request $request)
    {

        $currentYear = Carbon::now()->year;
        $queryBuilder = function ($classGradeId) use ($currentYear) {
            return DB::table('event_member_classes')
                ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                ->join('events', 'events.id', '=', 'event_members.event_id')
                ->join('users', 'event_members.member_id', '=', 'users.id')
                ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                ->where('events.date_time_start', 'like', '%' . $currentYear . '%')
                ->where('event_member_classes.class_grade_id', $classGradeId)
                ->where('event_member_classes.victory_point', '<>', 0)
                ->where('users.role_id', 6)
                ->where('included_in_leaderboard', 1)
                ->where('event_member_classes.competition_activity_id', 1)
                ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                ->orderByDesc('victory_points') // Changed to 'victory_points'
                ->limit(25)
                ->get();
        };

        $filter_consumer = $queryBuilder(1);
        $filter_prosumer = $queryBuilder(2);
        $filter_professional = $queryBuilder(3);

        $arr[] = $filter_consumer;
        $arr[] = $filter_prosumer;
        $arr[] = $filter_professional;

        return response()->json(['data' => $arr]);
    }

    public function listAllTopSixteenLeaderboardMenu()
    {
        $consumer = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.can_q_consumer_point AS victory_points'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            // ->where('email_verified_at', '<>', null)
            ->where('role_id', 6)
            // ->where('manual_input', 0)
            ->where('can_q_consumer_point', '>', 1)
            ->where('included_in_leaderboard', 1)
            ->orderBy('users.can_q_consumer_point', 'desc')
            ->orderBy('users.name', 'asc')
            ->take(25) //change from 16 to 25 at March 2024
            ->get();

        $prosumer = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.can_q_prosumer_point AS victory_points'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            // ->where('email_verified_at', '<>', null)
            ->where('role_id', 6)
            // ->where('manual_input', 0)
            ->where('can_q_prosumer_point', '>', 1)
            ->where('included_in_leaderboard', 1)
            ->orderBy('users.can_q_prosumer_point', 'desc')
            ->orderBy('users.name', 'asc')
            ->take(25)
            ->get();

        $professional = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.can_q_professional_point AS victory_points'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            // ->where('email_verified_at', '<>', null)
            ->where('role_id', 6)
            // ->where('manual_input', 0)
            ->where('can_q_professional_point', '>', 1)
            ->where('included_in_leaderboard', 1)
            ->orderBy('users.can_q_professional_point', 'desc')
            ->orderBy('users.name', 'asc')
            ->take(25)
            ->get();

        $arr[] = $consumer;
        $arr[] = $prosumer;
        $arr[] = $professional;

        // foreach ($user as $object) {
        //     $arr[] = $object->toArray();
        // }

        // for ($i = 0; $i < sizeof($arr); $i++) {
        //     $country_sponsor = $this->getCountryIdByUserId($arr[$i]['id']);
        //     if ($country_sponsor !== null) {
        //         $arr[$i]['country_id'] = $country_sponsor->country_id;
        //     } else {
        //         $arr[$i]['country_id'] = $country_sponsor;
        //     }
        // }
        return response()->json(['data' => $arr]);
    }

    public function listAllSponsorAdmin(Request $request)
    {
        $user = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            // 'users.country_id AS country_id',
            'country_sponsors.id AS country_id',
            'association_sponsors.id AS association_id',
            // 'countries.name AS country_sponsor_name',
            'associations.name AS sponsor_association_name',
            'countries.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->leftJoin('association_sponsors', 'association_sponsors.user_id', '=', 'users.id')
            ->leftJoin('associations', 'associations.id', '=', 'association_sponsors.association_id')
            ->where('email_verified_at', '<>', null)
            ->where(function ($query) use ($request) {
                $query->where('users.role_id', 4)
                    ->where('status_banned', 0)
                    ->where(function ($query) use ($request) {
                        $query->where('users.name', 'like', '%' . $request->search . '%')
                            ->orWhere('users.email', 'like', '%' . $request->search . '%')
                            ->orWhere('roles.role_name', 'like', '%' . $request->search . '%')
                            ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_types.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_tiers.name', 'like', '%' . $request->search . '%');
                    });
            })
            ->orWhere(function ($query) use ($request) {
                $query->where('users.role_id', 3)
                    ->where('status_banned', 0)
                    ->where('users.sponsor_type', '=', $request->sponsor_type)
                    ->where('users.association_id', '=', null)
                    ->where(function ($query) use ($request) {
                        $query->where('users.name', 'like', '%' . $request->search . '%')
                            ->orWhere('users.email', 'like', '%' . $request->search . '%')
                            ->orWhere('roles.role_name', 'like', '%' . $request->search . '%')
                            ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_types.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_tiers.name', 'like', '%' . $request->search . '%');
                    });
            })
            ->orWhere(function ($query) use ($request) {
                $query->where('users.role_id', 3)
                    ->where('status_banned', 0)
                    ->where('users.sponsor_type', '=', null)
                    ->where('users.association_id', '=', null)
                    ->where(function ($query) use ($request) {
                        $query->where('users.name', 'like', '%' . $request->search . '%')
                            ->orWhere('users.email', 'like', '%' . $request->search . '%')
                            ->orWhere('roles.role_name', 'like', '%' . $request->search . '%')
                            ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_types.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_tiers.name', 'like', '%' . $request->search . '%');
                    });
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $userCount = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            // 'users.country_id AS country_id',
            'country_sponsors.id AS country_id',
            'association_sponsors.id AS association_id',
            // 'countries.name AS country_sponsor_name',
            'associations.name AS sponsor_association_name',
            'countries.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->leftJoin('association_sponsors', 'association_sponsors.user_id', '=', 'users.id')
            ->leftJoin('associations', 'associations.id', '=', 'association_sponsors.association_id')
            ->where('email_verified_at', '<>', null)
            ->where(function ($query) use ($request) {
                $query->where('users.role_id', 4)
                    ->where('status_banned', 0)
                    ->where(function ($query) use ($request) {
                        $query->where('users.name', 'like', '%' . $request->search . '%')
                            ->orWhere('users.email', 'like', '%' . $request->search . '%')
                            ->orWhere('roles.role_name', 'like', '%' . $request->search . '%')
                            ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_types.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_tiers.name', 'like', '%' . $request->search . '%');
                    });
            })
            ->orWhere(function ($query) use ($request) {
                $query->where('users.role_id', 3)
                    ->where('status_banned', 0)
                    ->where('users.sponsor_type', '=', $request->sponsor_type)
                    ->where('users.association_id', '=', null)
                    ->where(function ($query) use ($request) {
                        $query->where('users.name', 'like', '%' . $request->search . '%')
                            ->orWhere('users.email', 'like', '%' . $request->search . '%')
                            ->orWhere('roles.role_name', 'like', '%' . $request->search . '%')
                            ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_types.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_tiers.name', 'like', '%' . $request->search . '%');
                    });
            })
            ->orWhere(function ($query) use ($request) {
                $query->where('users.role_id', 3)
                    ->where('status_banned', 0)
                    ->where('users.sponsor_type', '=', null)
                    ->where('users.association_id', '=', null)
                    ->where(function ($query) use ($request) {
                        $query->where('users.name', 'like', '%' . $request->search . '%')
                            ->orWhere('users.email', 'like', '%' . $request->search . '%')
                            ->orWhere('roles.role_name', 'like', '%' . $request->search . '%')
                            ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_types.name', 'like', '%' . $request->search . '%')
                            ->orWhere('sponsor_tiers.name', 'like', '%' . $request->search . '%');
                    });
            })
            ->count();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        // for ($i = 0; $i < sizeof($arr); $i++) {
        //     $country_sponsor = $this->getCountryIdByUserId($arr[$i]['id']);
        //     if ($country_sponsor !== null) {
        //         $arr[$i]['country_id'] = $country_sponsor->country_id;
        //     } else {
        //         $arr[$i]['country_id'] = $country_sponsor;
        //     }
        // }
        return response()->json(['data' => $arr, 'total' => $userCount]);
    }

    public function listAllSponsors(Request $request)
    {
        $countrySponsors = CountrySponsor::all()->pluck("user_id")->toArray();
        $associationSponsors = AssociationSponsor::all()->pluck("user_id")->toArray();

        $sponsorsId = array_merge($countrySponsors, $associationSponsors);

        $user = User::select(
            'users.id AS id',
            'users.name AS name',
            'user_profiles.avatar AS avatar',
            'email',
            'email_verified_at',
            'role_id',
            'country_sponsors.country_id AS country_id',
            'countries.name AS country_name',
            'association_sponsors.association_id AS association_id',
            'associations.name AS associations_name',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->leftJoin('association_sponsors', 'association_sponsors.user_id', '=', 'users.id')
            ->leftJoin('associations', 'associations.id', '=', 'association_sponsors.association_id')
            ->whereIn("users.id", $sponsorsId)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('countries.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $userCount = User::select(
            'id',
            'users.name AS name',
            'email',
            'user_profiles.avatar AS avatar',
            'email_verified_at',
            'role_id',
            'country_sponsors.country_id AS country_id',
            'countries.name AS country_name',
            'association_sponsors.association_id AS association_id',
            'associations.name AS associations_name',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->leftJoin("association_sponsors", "association_sponsors.user_id", "=", "users.id")
            ->leftJoin("associations", "associations.id", "=", "association_sponsors.association_id")
            ->whereIn("users.id", $sponsorsId)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('countries.name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $userCount]);
    }

    public function listGrouped(Request $request)
    {
        $meUser = User::select('grouped_user_id')->where('id', $request->id)->first();

        if ($meUser->grouped_user_id == null) {
            $grouped_user_id = 0;
        } else {
            $grouped_user_id = $meUser->grouped_user_id;
        }

        $user = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'user_profiles.phone_no AS phone_no',
            'user_profiles.phone_verified_at AS phone_verified_at',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'users.country_id AS user_country_id',
            'c2.name AS user_country_name',
            'country_sponsors.id AS country_id',
            // 'countries.name AS country_sponsor_name',
            'c1.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries AS c1', 'c1.id', '=', 'country_sponsors.country_id')
            ->leftJoin('countries AS c2', 'c2.id', '=', 'users.country_id')
            ->where('email_verified_at', '<>', null)
            // ->where('grouped_user_id', '<>', null)
            ->where('grouped_user_id', $grouped_user_id)
            ->where('users.id', '<>', $request->id)
            // ->where(function ($query) use ($meUser) {
            //     $query->where('grouped_user_id', $meUser->grouped_user_id);
            //         // ->orWhere('users.id', $request->id);
            // })
            ->get();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr]);
    }

    public function listUngrouped(Request $request)
    {
        $user = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'user_profiles.phone_no AS phone_no',
            'user_profiles.phone_verified_at AS phone_verified_at',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            'users.country_id AS user_country_id',
            'c2.name AS user_country_name',
            'country_sponsors.id AS country_id',
            // 'countries.name AS country_sponsor_name',
            'c1.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries AS c1', 'c1.id', '=', 'country_sponsors.country_id')
            ->leftJoin('countries AS c2', 'c2.id', '=', 'users.country_id')
            ->where('email_verified_at', '<>', null)
            ->where('grouped_user_id', null)
            ->where('users.role_id', '<>', 1)
            ->where('users.id', '<>', $request->id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('roles.role_name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $userCount = User::select(
            'users.id AS id',
            'users.name',
            'user_profiles.avatar AS avatar',
            'users.email',
            'users.email_verified_at',
            'users.role_id AS role_id',
            'users.manual_input AS manual_input',
            // 'users.country_id AS country_id',
            'country_sponsors.id AS country_id',
            // 'countries.name AS country_sponsor_name',
            'countries.name AS sponsor_country_name',
            'roles.role_name',
            'sponsor_types.id AS sponsor_type',
            'sponsor_types.name AS sponsor_type_name',
            'sponsor_tiers.id AS sponsor_tier',
            'sponsor_tiers.name AS sponsor_tier_name',
            'can_q_consumer_point',
            'can_q_prosumer_point',
            'can_q_professional_point',
            'status_banned',
            'users.created_at',
            'users.included_in_leaderboard'
        )
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('sponsor_types', 'sponsor_types.id', '=', 'users.sponsor_type')
            ->leftJoin('sponsor_tiers', 'sponsor_tiers.id', '=', 'users.sponsor_tier')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'users.id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->where('email_verified_at', '<>', null)
            ->where('grouped_user_id', null)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('users.email', 'like', '%' . $request->search . '%')
                    ->orWhere('roles.role_name', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($user as $object) {
            $arr[] = $object->toArray();
        }

        return response()->json(['data' => $arr, 'total' => $userCount]);
    }

    public function userBanned(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status_banned' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->id)->first();

        $banned = (int) $request->status_banned;

        if ($request->status_banned !== 0 && $request->status_banned !== 1) {
            return response()->json(['status' => 'failed', 'message' => 'wrong parameter of status_banned'], 200);
        }

        if ($user) {
            $status_banned = (int) $user->status_banned;

            if ($status_banned === $banned && $status_banned === 1) {
                return response()->json(['status' => 'failed', 'message' => 'user already banned'], 200);
            }

            if ($status_banned === $banned && $status_banned === 0) {
                return response()->json(['status' => 'failed', 'message' => 'user already unbanned'], 200);
            }

            $user->status_banned = $banned;
            $user->save();
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }

        if ($banned === 1) {
            return response()->json(['status' => 'success', 'message' => 'user role have been banned successfully'], 200);
        } else {
            return response()->json(['status' => 'success', 'message' => 'user role have been retract successfully'], 200);
        }
    }

    public function userLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'link_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->link_user_id)->first();

        if ($user) {
            $grouped_user_id = $user->grouped_user_id;

            if ($grouped_user_id != null) {
                return response()->json(['status' => 'failed', 'message' => 'user already link with another account'], 200);
            }

            $userMain = User::where('id', $request->id)->first();
            $userMain->grouped_user_id = $request->id;
            $userMain->save();

            $userMainProfile = UserProfile::where('user_id', $userMain->id)->first();

            $user->grouped_user_id = $request->id;
            $user->save();
            $userProfile = UserProfile::where('user_id', $user->id)->first();
            if ($userProfile->phone_verified_at == null) {
                $userProfile->phone_no = $userMainProfile->phone_no;
                $userProfile->save();
            }

            return response()->json(['status' => 'success', 'message' => 'user successfully linked'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }
    }

    public function userUnlink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'link_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::where('id', $request->id)->first();

        if ($user) {

            $user->grouped_user_id = null;
            $user->save();

            $userProfile = UserProfile::where('user_id', $user->id)->first();
            if ($userProfile->phone_verified_at == null) {
                $userProfile->phone_no = null;
                $userProfile->save();
            }

            $usersSelected = User::where('grouped_user_id', $request->link_user_id)->get();

            $userMainGroup = User::where('id', $request->id)->where('grouped_user_id', $request->id)->first();

            if (!$userMainGroup) {
                $userUpdate = User::where('grouped_user_id', $request->id)->get();

                $linkUserProfile = UserProfile::where('user_id', $request->link_user_id)->first();

                foreach ($userUpdate as $u) {
                    UserProfile::where('user_id', $u->id)->where('phone_verified_at', null)->update(['phone_no' => $linkUserProfile->phone_no]);
                }

                User::where('grouped_user_id', $request->id)->update(['grouped_user_id' => $request->link_user_id]);
            }
            if (sizeof($usersSelected) == 1) {
                $userSelectedUpdate = User::where('grouped_user_id', $request->link_user_id)->first();

                $userSelectedUpdate->grouped_user_id = null;
                $userSelectedUpdate->save();

                // $userSelectedProfileUpdate = UserProfile::where('user_id', $userSelectedUpdate->id)->first();
                // $userSelectedProfileUpdate->phone_no = null;
                // $userSelectedProfileUpdate->save();

                // return response()->json(['status' => 'success', 'message' => $usersSelected], 200);
                // $userMainGroup->update(['grouped_user_id' => null]);
            }

            return response()->json(['status' => 'success', 'message' => 'user successfully un-linked', 'users' => $usersSelected, 'userMainGroup' => $userMainGroup], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
        }
    }

    public function getCountryIdByUserId($user_id)
    {
        $countrySponsor = CountrySponsor::select(
            'country_id'
        )
            ->where('user_id', '=', $user_id)
            ->first();

        return $countrySponsor;
    }

    public function switchUser(Request $request)
    {

        $user = User::where('id', $request->id)->first();

        // $user = $this->guard()->user();
        // $user->generateToken();

        $userDetail = $user;

        $userProfile = UserProfile::where('user_id', $userDetail->id)->first();

        $userDetail['avatar'] = $userProfile->avatar;
        $userDetail['banner'] = $userProfile->banner;
        $userDetail['phone'] = $userProfile->phone_no;
        $userDetail['phone_verified_at'] = $userProfile->phone_verified_at;
        $userDetail['biography'] = $userProfile->biography;

        return response()->json([
            'status' => 'success',
            'data' => $userDetail,
            // 'token' => $user->createToken('MyApp')->accessToken,
            // 'token' => $user->api_token,
        ]);
    }

    public function userVerify(Request $request, User $user)
    {
        try {
            $user->userProfile()->update([
                'phone_verified_at' => date('Y-m-d H:i:s')
            ]);
            $message = [
                'status' => 'success',
                'message' => 'verify success'
            ];
        } catch (\Exception $exception) {
            $message = [
                'status' => 'failed',
                'message' => $exception->getMessage()
            ];
        }
        return response()->json($message);
    }

    public function includeToLeaderBoard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $update_user_leaderboard_status = User::where('id', $request->id)->update([
            'included_in_leaderboard' => $request->status
        ]);

        if ($update_user_leaderboard_status) {
            return response()->json([
                'status' => "success",
                'message' => "User include in leaderboard status successfully updated",
            ], 200);
        } else {
            return response()->json([
                'status' => "failed",
                'message' => "User include in leaderboard status failed to update",
            ], 401);
        }
    }

    public function forceVerifyNumber(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $userProfile = UserProfile::where('user_id', $request->id)->first();

        if ($userProfile) {
            $isVerify = $userProfile->phone_verified_at;
            
            if ($isVerify === null) {
                UserProfile::where('user_id', $request->id)->update([
                    'phone_verified_at' => now()
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => "Forced Verification Successful"
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User phone number already verified'
                ]);
            }
        }
    }

    public function filterLeaderboard(Request $request)
    {
        // Country filter
        if ($request->country_id !== null && $request->zone_id === null &&  $request->eventYear === null) {
            // Define a reusable function to generate the query
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->where(function ($query) use ($request) {
                        $query->where('events.event_country_id', $request->country_id)
                            ->orWhere('events.event_countries_id', $request->country_id);
                    })
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points') // Changed to 'victory_points'
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_professional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_professional;

            return response()->json(['data' => $arr]);
        }

        // Zone filter
        if ($request->country_id === null && $request->zone_id !== null && $request->eventYear === null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->where('events.zone', $request->zone_id)
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points') // Changed to 'victory_points'
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_professional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_professional;

            return response()->json(['data' => $arr]);
        }

        // Country Zone Only Year
        if ($request->country_id === null && $request->zone_id === null &&  $request->eventYear !== null) {
            // Define a reusable function to generate the query
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->where('events.date_time_start', 'like', '%' . $request->eventYear . '%')
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points') // Changed to 'victory_points'
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_professional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_professional;

            return response()->json(['data' => $arr]);
        }

        // Country and Zone filter
        if ($request->country_id !== null && $request->zone_id !== null && $request->eventYear === null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->where(function ($query) use ($request) {
                        $query->where('events.event_country_id', $request->country_id)
                            ->orWhere('events.event_countries_id', $request->country_id);
                    })
                    ->where('events.zone', $request->zone_id)
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points') // Changed to 'victory_points'
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_professional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_professional;

            return response()->json(['data' => $arr]);
        }

        // Country and Year filter
        if ($request->country_id !== null && $request->zone_id === null && $request->eventYear !== null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->where(function ($query) use ($request) {
                        $query->where('events.event_country_id', $request->country_id)
                            ->orWhere('events.event_countries_id', $request->country_id);
                    })
                    ->where('events.date_time_start', 'like', '%' . $request->eventYear . '%')
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points') // Changed to 'victory_points'
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_professional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_professional;

            return response()->json(['data' => $arr]);
        }

        // Zone and Year filter
        if ($request->country_id === null && $request->zone_id !== null && $request->eventYear !== null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    // ->where('events.event_country_id', $request->country_id)
                    ->where('events.date_time_start', 'like', '%' . $request->eventYear . '%')
                    ->where('events.zone', $request->zone_id)
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points') // Changed to 'victory_points'
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_professional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_professional;

            return response()->json(['data' => $arr]);
        }

        // Country, Zone and Year filter
        if ($request->country_id !== null && $request->zone_id !== null && $request->eventYear !== null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->where(function ($query) use ($request) {
                        $query->where('events.event_country_id', $request->country_id)
                            ->orWhere('events.event_countries_id', $request->country_id);
                    })
                    ->where('events.date_time_start', 'like', '%' . $request->eventYear . '%')
                    ->where('events.zone', $request->zone_id)
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points') // Changed to 'victory_points'
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_professional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_professional;

            return response()->json(['data' => $arr]);
        }

        // Year filter
        if ($request->year != null && $request->tag_id == null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->join('event_tag_groups', 'events.tag', '=', 'event_tag_groups.id')
                    ->join('custom_event_tags', 'event_tag_groups.tag_id', '=', 'custom_event_tags.id')
                    // ->where('custom_event_tags.id', $request->tag_id)
                    ->where('custom_event_tags.year', $request->year)
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points')
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_profesional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_profesional;

            return response()->json(['data' => $arr]);
        }

        // Year and Tag filter
        if ($request->year != null && $request->tag_id != null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->join('event_tag_groups', 'events.id', '=', 'event_tag_groups.event_id')
                    ->join('custom_event_tags', 'event_tag_groups.tag_id', '=', 'custom_event_tags.id')
                    // ->where('events.date_start', 'like', '%' . $request->year . '%')
                    ->where('custom_event_tags.id', $request->tag_id)
                    ->where('custom_event_tags.year', $request->year)
                    // ->where('custom_event_tags.year', $request->year)
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points')
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_profesional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_profesional;

            return response()->json(['data' => $arr]);
        }

        // Tag Filter
        if ($request->year == null && $request->tag_id != null) {
            $queryBuilder = function ($classGradeId) use ($request) {
                return DB::table('event_member_classes')
                    ->select('users.id AS id', 'users.name', 'user_profiles.avatar AS avatar', DB::raw('SUM(event_member_classes.victory_point) AS victory_points'))
                    ->join('event_members', 'event_member_classes.event_member_id', '=', 'event_members.id')
                    ->join('events', 'events.id', '=', 'event_members.event_id')
                    ->join('users', 'event_members.member_id', '=', 'users.id')
                    ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                    ->join('event_tag_groups', 'events.id', '=', 'event_tag_groups.event_id')
                    ->join('custom_event_tags', 'event_tag_groups.tag_id', '=', 'custom_event_tags.id')
                    // ->where('events.date_start', 'like', '%' . $request->year . '%')
                    ->where('custom_event_tags.id', $request->tag_id)
                    ->where('event_member_classes.class_grade_id', $classGradeId)
                    ->where('event_member_classes.victory_point', '<>', 0)
                    ->where('users.role_id', 6)
                    ->where('included_in_leaderboard', 1)
                    ->where('event_member_classes.competition_activity_id', 1)
                    ->groupBy('users.id', 'users.name', 'user_profiles.avatar')
                    ->orderByDesc('victory_points')
                    ->limit(25)
                    ->get();
            };

            $filter_consumer = $queryBuilder(1);
            $filter_prosumer = $queryBuilder(2);
            $filter_profesional = $queryBuilder(3);

            $arr[] = $filter_consumer;
            $arr[] = $filter_prosumer;
            $arr[] = $filter_profesional;

            return response()->json(['data' => $arr]);
        }
    }
}
