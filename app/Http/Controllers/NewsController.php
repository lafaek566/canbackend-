<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public $successStatus = 200;

    public function listAll(Request $request)
    {
        if ($request->user_id !== null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                // ->with('user')
                // ->with('updatedByUser')
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->where('news.user_id', '=', $request->user_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                // ->with('user')
                // ->with('updatedByUser')
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->join('countries', 'countries.id', '=', 'news.country_id')
                ->where('news.user_id', '=', $request->user_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        } else {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                // ->whereIn('user.role_id', [1, 2])
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                // ->whereIn('user.role_id', [1, 2])
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        }

        $arr = [];

        foreach ($news as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $newsCount, 'userid' => $request->user_id]);
    }

    public function listAllByCountryId(Request $request)
    {

        if ($request->country_id !== null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->where('news.country_id', '=', $request->country_id)
                ->where('user.role_id', '<>', 3)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->where('news.country_id', '=', $request->country_id)
                ->where('user.role_id', '<>', 3)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        } else if ($request->user_id !== null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->where('news.user_id', '=', $request->user_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->where('news.user_id', '=', $request->user_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        } else if ($request->tier_id !== null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->where('user.sponsor_tier', '=', $request->tier_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->latest('created_at')->first();
            // ->offset($request->offset)
            // ->limit($request->limit)
            // ->orderBy('news.created_at', 'desc')
            // ->get();

            $newsCount = 1;
            // $newsCount = News::select(
            //     'news.id AS id',
            //     'news.title AS title',
            //     'news.subtitle AS subtitle',
            //     'news.content AS content',
            //     'news.thumbnail AS thumbnail',
            //     'news.user_id AS user_id',
            //     'news.country_id AS country_id',
            //     'countries.name AS country_name',
            //     'user.name AS author',
            //     'user_profiles.avatar AS user_avatar',
            //     'news.date AS date',
            //     'news.updated_at AS updated_at',
            //     'news.created_at AS created_at'
            // )

            //     ->join('users', 'users.id', '=', 'news.user_id')
            //     ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            //     ->join('countries', 'countries.id', '=', 'news.country_id')
            //     ->where('user.sponsor_tier', '=', $request->tier_id)
            //     ->where(function ($query) use ($request) {
            //         $query->where('news.title', 'like', '%' . $request->search . '%')
            //             ->orWhere('countries.name', 'like', '%' . $request->search . '%')
            //             ->orWhere('user.name', 'like', '%' . $request->search . '%');
            //     })
            //     ->orderBy('news.created_at', 'desc')
            //     ->count();

            return response()->json(['data' => $news]);

            exit();
        } else {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->whereIn('user.role_id', [1, 2, 9, 10, 11])
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->whereIn('user.role_id', [1, 2, 9, 10, 11])
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        }

        $arr = [];

        foreach ($news as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $newsCount, 'userid' => $request->user_id]);
    }

    public function listAllByAssociationId(Request $request)
    {

        if ($request->association_id !== null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'news.association_id AS association_id',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->join('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->where('news.association_id', '=', $request->association_id)
                ->where('user.role_id', '<>', 3)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->join('associations', 'associations.id', '=', 'news.association_id')
                ->where('news.association_id', '=', $request->association_id)
                ->where('user.role_id', '<>', 3)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        } else if ($request->user_id !== null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.association_id AS association_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->where('news.user_id', '=', $request->user_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->where('news.user_id', '=', $request->user_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        } else if ($request->tier_id !== null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->where('user.sponsor_tier', '=', $request->tier_id)
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->latest('created_at')->first();

            $newsCount = 1;
            return response()->json(['data' => $news]);

            exit();
        } else {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->whereIn('user.role_id', [1, 2, 9, 10, 11])
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();

            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.association_id AS association_id',
                'news.country_id AS country_id',
                'associations.name AS association_name',
                'countries.name AS country_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->whereIn('user.role_id', [1, 2, 9, 10])
                ->where(function ($query) use ($request) {
                    $query->where('news.title', 'like', '%' . $request->search . '%')
                        ->orWhere('associations.name', 'like', '%' . $request->search . '%')
                        ->orWhere('user.name', 'like', '%' . $request->search . '%');
                })
                ->orderBy('news.date', 'desc')
                ->count();
        }

        $arr = [];

        foreach ($news as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $newsCount, 'userid' => $request->user_id]);
    }

    public function listAllBySponsor(Request $request)
    {
        $arr = json_decode($request->sponsor_tiers, true);

        // return response()->json(['data' => $request->sponsor_tiers, 'arr' => $arr]);

        $arr_sponsor_news = [];

        for ($i = 0; $i < sizeof($arr); $i++) {
            $sponsor_tier = $arr[$i]['sponsor_tier'];
            $amount = $arr[$i]['amount'];
            $country_id = $arr[$i]['country_id'];
            $sponsor_type = $arr[$i]['sponsor_type'];


            if ($country_id != null) {
                $newsSponsorCountry = News::select(
                    'news.id AS id',
                    'news.title AS title',
                    'news.subtitle AS subtitle',
                    'news.content AS content',
                    'news.thumbnail AS thumbnail',
                    'news.user_id AS user_id',
                    'news.updated_by_user_id AS updated_by_user_id',
                    'news.country_id AS country_id',
                    'countries.name AS country_name',
                    'user.name AS author',
                    'user_profile.avatar AS user_avatar',
                    'updated_by_user.name AS updated_by_user_name',
                    'updated_by_user_profile.avatar AS updated_by_user_avatar',
                    'news.date AS date',
                    'news.updated_at AS updated_at',
                    'news.created_at AS created_at'
                )
                    ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                    ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                    ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                    ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                    ->join('countries', 'countries.id', '=', 'news.country_id')
                    ->where('user.sponsor_tier', '=', $sponsor_tier)
                    ->where('news.country_id', '=', $country_id)
                    // ->whereIn('user.sponsor_type', [2, 3])
                    ->latest('news.created_at')
                    ->take($amount)
                    ->get();


                $newsSponsorGlobal = News::select(
                    'news.id AS id',
                    'news.title AS title',
                    'news.subtitle AS subtitle',
                    'news.content AS content',
                    'news.thumbnail AS thumbnail',
                    'news.user_id AS user_id',
                    'news.updated_by_user_id AS updated_by_user_id',
                    'news.country_id AS country_id',
                    'countries.name AS country_name',
                    'user.name AS author',
                    'user_profile.avatar AS user_avatar',
                    'updated_by_user.name AS updated_by_user_name',
                    'updated_by_user_profile.avatar AS updated_by_user_avatar',
                    'news.date AS date',
                    'news.updated_at AS updated_at',
                    'news.created_at AS created_at'
                )
                    ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                    ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                    ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                    ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                    ->join('countries', 'countries.id', '=', 'news.country_id')
                    ->where('user.sponsor_tier', '=', $sponsor_tier)
                    // ->where('news.country_id', '=', $country_id)
                    ->where('user.sponsor_type', '=', 1)
                    ->latest('news.created_at')
                    ->take($amount)
                    ->get();

                $news = [];

                $newsSponsorCountry = $newsSponsorCountry->toArray();
                $newsSponsorGlobal = $newsSponsorGlobal->toArray();

                if (!empty($newsSponsorCountry) && !empty($newsSponsorGlobal)) {

                    // $dateNewsSponsorCountry = Carbon::parse($newsSponsorCountry[0]['created_at']);
                    // $dateSponsorGlobal = Carbon::parse($newsSponsorGlobal[0]['created_at']);

                    // if ($dateNewsSponsorCountry > $dateSponsorGlobal) {
                    //     $news = $newsSponsorCountry;
                    // } else {
                    //     $news = $newsSponsorGlobal;
                    // }

                    // $news['newsCountry'] = $newsCountry;
                    // $news['newsSponsorType'] = $newsSponsorType;

                    $original = new Collection($newsSponsorCountry);

                    $latest = new Collection($newsSponsorGlobal);

                    $news = $original->merge($latest);

                    //use laravel collection sort method to sort the collection by created_at
                    $news = $news->unique('id')->sortByDesc('created_at')->take($amount);

                    $news = $news->values()->all();
                } else if (!empty($newsSponsorCountry)) {
                    $news = $newsSponsorCountry;
                } else if (!empty($newsSponsorGlobal)) {
                    $news = $newsSponsorGlobal;
                }

                // $news['newsCountry'] = $newsCountry;
                // $news['newsSponsorType'] = $newsSponsorType;

                // return response()->json(['data' => $news]);
            } else {
                $news = News::select(
                    'news.id AS id',
                    'news.title AS title',
                    'news.subtitle AS subtitle',
                    'news.content AS content',
                    'news.thumbnail AS thumbnail',
                    'news.user_id AS user_id',
                    'news.updated_by_user_id AS updated_by_user_id',
                    'news.country_id AS country_id',
                    'countries.name AS country_name',
                    'user.name AS author',
                    'user_profile.avatar AS user_avatar',
                    'updated_by_user.name AS updated_by_user_name',
                    'updated_by_user_profile.avatar AS updated_by_user_avatar',
                    'news.date AS date',
                    'news.updated_at AS updated_at',
                    'news.created_at AS created_at'
                )
                    ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                    ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                    ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                    ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                    ->join('countries', 'countries.id', '=', 'news.country_id')
                    ->where('user.sponsor_tier', '=', $sponsor_tier)
                    ->where('user.sponsor_type', '=', $sponsor_type)
                    ->latest('created_at')
                    ->take($amount)
                    ->get();
            }

            switch ($sponsor_tier) {
                case 1:
                    $arr_sponsor_news['sponsor_very_big'] = $news;
                    break;
                case 2:
                    $arr_sponsor_news['sponsor_above_big'] = $news;
                    break;
                case 3:
                    $arr_sponsor_news['sponsor_big'] = $news;
                    break;
                case 4:
                    $arr_sponsor_news['sponsor_small'] = $news;
                    break;
                default:
            }
        }

        return response()->json(['data' => $arr_sponsor_news]);
    }

    public function listAllBySponsorTier(Request $request)
    {

        $sponsor_tier = $request->sponsor_tier;
        $country_id = $request->country_id;
        $sponsor_type = $request->sponsor_type;

        if ($country_id != null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->join('countries', 'countries.id', '=', 'news.country_id')
                ->where('user.sponsor_tier', '=', $sponsor_tier)
                ->where('news.country_id', '=', $country_id)
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();


            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->join('countries', 'countries.id', '=', 'news.country_id')
                ->where('user.sponsor_tier', '=', $sponsor_tier)
                ->where('news.country_id', '=', $country_id)
                ->orderBy('news.date', 'desc')
                ->count();
        } else {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->join('countries', 'countries.id', '=', 'news.country_id')
                ->where('user.sponsor_tier', '=', $sponsor_tier)
                ->where('user.sponsor_type', '=', 1)
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();


            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->join('countries', 'countries.id', '=', 'news.country_id')
                ->where('user.sponsor_tier', '=', $sponsor_tier)
                ->where('user.sponsor_type', '=', 1)
                ->orderBy('news.date', 'desc')
                ->count();
        }


        $arr = [];

        foreach ($news as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $newsCount]);
    }

    public function listAllByAssociationSponsor(Request $request)
    {

        $association_id = $request->association_id;

        if ($association_id != null) {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'news.association_id AS association_id',
                'countries.name AS country_name',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->join('associations', 'associations.id', '=', 'news.association_id')
                ->where('news.association_id', "=", $association_id)
                ->where('user.association_id', "=", $association_id)
                ->where('user.role_id', '=', 3)
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();


            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'news.association_id AS association_id',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
                ->join('associations', 'associations.id', '=', 'news.association_id')
                ->where('news.association_id', "=", $association_id)
                ->where('user.association_id', "=", $association_id)
                ->where('user.role_id', '=', 3)
                ->orderBy('news.date', 'desc')
                ->count();
        } else {
            $news = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'news.association_id AS association_id',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->join('countries', 'countries.id', '=', 'news.country_id')
                ->join('associations', 'associations.id', '=', 'news.association_id')
                ->where('user.association_id', "=", $association_id)
                ->where('user.role_id', '=', 3)
                ->offset($request->offset)
                ->limit($request->limit)
                ->orderBy('news.date', 'desc')
                ->get();


            $newsCount = News::select(
                'news.id AS id',
                'news.title AS title',
                'news.subtitle AS subtitle',
                'news.content AS content',
                'news.thumbnail AS thumbnail',
                'news.user_id AS user_id',
                'news.updated_by_user_id AS updated_by_user_id',
                'news.country_id AS country_id',
                'countries.name AS country_name',
                'news.association_id AS association_id',
                'associations.name AS association_name',
                'user.name AS author',
                'user_profile.avatar AS user_avatar',
                'updated_by_user.name AS updated_by_user_name',
                'updated_by_user_profile.avatar AS updated_by_user_avatar',
                'news.date AS date',
                'news.updated_at AS updated_at',
                'news.created_at AS created_at'
            )
                ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
                ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
                ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
                ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
                ->join('countries', 'countries.id', '=', 'news.country_id')
                ->join('associations', 'associations.id', '=', 'news.association_id')
                ->where('user.association_id', "=", $association_id)
                ->where('user.role_id', '=', 3)
                ->orderBy('news.date', 'desc')
                ->count();
        }


        $arr = [];

        foreach ($news as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $newsCount]);
    }

    public function listAllBySponsorId(Request $request)
    {
        $news = News::select(
            'news.id AS id',
            'news.title AS title',
            'news.subtitle AS subtitle',
            'news.content AS content',
            'news.thumbnail AS thumbnail',
            'news.user_id AS user_id',
            'news.updated_by_user_id AS updated_by_user_id',
            'news.country_id AS country_id',
            'countries.name AS country_name',
            'associations.name AS associations_name',
            'user.name AS author',
            'user_profile.avatar AS user_avatar',
            'updated_by_user.name AS updated_by_user_name',
            'updated_by_user_profile.avatar AS updated_by_user_avatar',
            'news.date AS date',
            'news.updated_at AS updated_at',
            'news.created_at AS created_at'
        )
            // ->with('user')
            // ->with('updatedByUser')
            ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
            ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
            ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
            ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
            ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
            ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
            ->where('news.user_id', '=', $request->user_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('news.date', 'desc')
            ->get();

        $newsCount = News::select(
            'news.id AS id',
            'news.title AS title',
            'news.subtitle AS subtitle',
            'news.content AS content',
            'news.thumbnail AS thumbnail',
            'news.user_id AS user_id',
            'news.updated_by_user_id AS updated_by_user_id',
            'news.country_id AS country_id',
            'countries.name AS country_name',
            'associations.name AS associations_name',
            'user.name AS author',
            'user_profile.avatar AS user_avatar',
            'updated_by_user.name AS updated_by_user_name',
            'updated_by_user_profile.avatar AS updated_by_user_avatar',
            'news.date AS date',
            'news.updated_at AS updated_at',
            'news.created_at AS created_at'
        )
            // ->with('user')
            // ->with('updatedByUser')
            ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
            ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
            ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
            ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
            ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
            ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
            ->where('news.user_id', '=', $request->user_id)
            ->orderBy('news.date', 'desc')
            ->count();

        $arr = [];

        foreach ($news as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $newsCount, 'userid' => $request->user_id]);
    }

    protected function getNewsByTierId()
    {
    }

    public function listAllBySponsorAdmin(Request $request)
    {
        $users = User::select(
            'id'
        )
            ->where('sponsor_type', $request->sponsor_type)
            ->get();

        $usersId = [];

        foreach ($users as $object) {
            $usersId[] = $object->toArray();
        }

        $news = News::select(
            'news.id AS id',
            'news.title AS title',
            'news.subtitle AS subtitle',
            'news.content AS content',
            'news.thumbnail AS thumbnail',
            'news.user_id AS user_id',
            'news.updated_by_user_id AS updated_by_user_id',
            'news.country_id AS country_id',
            'countries.name AS country_name',
            'user.name AS author',
            'user_profile.avatar AS user_avatar',
            'updated_by_user.name AS updated_by_user_name',
            'updated_by_user_profile.avatar AS updated_by_user_avatar',
            'news.date AS date',
            'news.updated_at AS updated_at',
            'news.created_at AS created_at'
        )
            ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
            ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
            ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
            ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
            ->join('countries', 'countries.id', '=', 'news.country_id')
            ->whereIn('news.user_id', $usersId)
            ->where(function ($query) use ($request) {
                $query->where('news.title', 'like', '%' . $request->search . '%')
                    ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                    ->orWhere('user.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('news.date', 'desc')
            ->get();

        $newsCount = News::select(
            'news.id AS id',
            'news.title AS title',
            'news.subtitle AS subtitle',
            'news.content AS content',
            'news.thumbnail AS thumbnail',
            'news.user_id AS user_id',
            'news.updated_by_user_id AS updated_by_user_id',
            'news.country_id AS country_id',
            'countries.name AS country_name',
            'user.name AS author',
            'user_profile.avatar AS user_avatar',
            'updated_by_user.name AS updated_by_user_name',
            'updated_by_user_profile.avatar AS updated_by_user_avatar',
            'news.date AS date',
            'news.updated_at AS updated_at',
            'news.created_at AS created_at'
        )
            ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
            ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
            ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
            ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
            ->join('countries', 'countries.id', '=', 'news.country_id')
            ->whereIn('news.user_id', $usersId)
            ->where(function ($query) use ($request) {
                $query->where('news.title', 'like', '%' . $request->search . '%')
                    ->orWhere('countries.name', 'like', '%' . $request->search . '%')
                    ->orWhere('user.name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('news.date', 'desc')
            ->count();

        $arr = [];

        foreach ($news as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $newsCount, 'sponsor_type' => $request->sponsor_type, 'userids' => $usersId]);
    }

    public function listDetail(Request $request)
    {
        $news = News::select(
            'news.id AS id',
            'news.title AS title',
            'news.subtitle AS subtitle',
            'news.content AS content',
            'news.thumbnail AS thumbnail',
            'news.user_id AS user_id',
            'news.updated_by_user_id AS updated_by_user_id',
            'news.country_id AS country_id',
            'countries.name AS country_name',
            'news.association_id AS association_id',
            'associations.name AS association_name',
            'user.name AS author',
            'user_profile.avatar AS user_avatar',
            'updated_by_user.name AS updated_by_user_name',
            'updated_by_user_profile.avatar AS updated_by_user_avatar',
            'news.date AS date',
            'news.updated_at AS updated_at',
            'news.created_at AS created_at'
        )
            ->leftJoin('users AS user', 'user.id', '=', 'news.user_id')
            ->leftJoin('users AS updated_by_user', 'updated_by_user.id', '=', 'news.updated_by_user_id')
            ->leftJoin('user_profiles AS user_profile', 'user_profile.user_id', '=', 'user.id')
            ->leftJoin('user_profiles AS updated_by_user_profile', 'updated_by_user_profile.user_id', '=', 'updated_by_user.id')
            ->leftJoin('associations', 'associations.id', '=', 'news.association_id')
            ->leftJoin('countries', 'countries.id', '=', 'news.country_id')
            ->where('news.id', '=', $request->id)
            ->first();

        return response()->json(['data' => $news]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:news',
            'subtitle' => 'required',
            'content' => 'required',
            'user_id' => 'required',
            'updated_by_user_id' => 'required',
            'date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // BEGIN STORE

        $uploadPath = public_path('upload/');
        $storagePath = public_path('upload/files/');
        $newsPath = public_path('upload/files/news/');

        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');
        $random = str_random(50);
        $title = $random . $waktu;

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath);
        }

        if (!file_exists($storagePath)) {
            mkdir($storagePath);
        }

        if (!file_exists($newsPath)) {
            mkdir($newsPath);
        }

        if ($request->thumbnail !== '' && $request->thumbnail !== null) {
            $image_parts = explode(";base64,", $request->thumbnail);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileThumbnailLink = $newsPath . $title . '.' . $image_type;
            $fileThumbnailUrl = $protocol . '://' . $domain . '/upload/files/news/' . $title . '.' . $image_type;
            file_put_contents($fileThumbnailLink, $image_base64);
        } else {
            $fileThumbnailLink = '';
            $fileThumbnailUrl = '';
        }

        $input = $request->all();

        $input['thumbnail'] = $fileThumbnailUrl;
        // $date = \Carbon\Carbon::parse($input['date']);
        // $input['date'] = $date->format('Y-m-d');

        $saveNews = News::create($input);

        if ($saveNews) {
            return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, News $news)
    {
        $news = News::select('id', 'title')
            ->where('title', $request->title)
            ->get();

        $newsOld = News::select(
            'id',
            'title',
            'subtitle',
            'content',
            'thumbnail',
            'user_id',
            'date'
        )
            ->where('id', $request->id)
            ->get();

        $count = $news->count();

        if ($count > 0) {
            if ($newsOld[0]->id !== $news[0]->id) {
                return response()->json(['status' => 'failed', 'message' => 'title have been used'], 200);
            } else {
                return $this->updateNews($request, $newsOld);
            }
        } else {
            return $this->updateNews($request, $newsOld);
        }
    }

    protected function updateNews(Request $request, $newsOld)
    {
        $storagePath = public_path('upload/files/');
        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');
        $random = str_random(50);
        $title = $random . $waktu;

        $cekImage = substr($request['thumbnail'], 0, 10);

        if ($cekImage === 'data:image') {
            if ($newsOld[0]->thumbnail !== '' && $newsOld[0]->thumbnail !== null) {
                $path = parse_url($newsOld[0]->thumbnail, PHP_URL_PATH);
                $file_name = basename($path);
                $fileThumbnailLink = $storagePath . 'news/' . $file_name;

                if (file_exists($fileThumbnailLink)) {
                    unlink($fileThumbnailLink);
                }
            }
            $image_parts = explode(";base64,", $request->thumbnail);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileThumbnailLink = $storagePath . 'news/' . $title . '.' . $image_type;
            $fileThumbnailUrl = $protocol . '://' . $domain . '/upload/files/news/' . $title . '.' . $image_type;
            file_put_contents($fileThumbnailLink, $image_base64);
        } else {
            $fileThumbnailUrl = $newsOld[0]->thumbnail;
        }

        $newsUpdate['thumbnail'] = $fileThumbnailUrl;
        $newsUpdate['title'] = $request->title;
        $newsUpdate['subtitle'] = $request->subtitle;
        $newsUpdate['content'] = $request->content;
        // $newsUpdate['user_id'] = $request->user_id;
        $newsUpdate['updated_by_user_id'] = $request->updated_by_user_id;
        $newsUpdate['country_id'] = $request->country_id;
        $newsUpdate['association_id'] = $request->association_id;
        $newsUpdate['date'] = $request->date;

        $update = News::where('id', $request->id)->update(
            [
                'thumbnail' => $newsUpdate['thumbnail'],
                'title' => $newsUpdate['title'],
                'subtitle' => $newsUpdate['subtitle'],
                'content' => $newsUpdate['content'],
                // 'user_id' => $newsUpdate['user_id'],
                'updated_by_user_id' => $newsUpdate['updated_by_user_id'],
                'country_id' => $newsUpdate['country_id'],
                'association_id' => $newsUpdate['association_id'],
                'date' => $newsUpdate['date']
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    public function delete(Request $request, News $news)
    {
        $news = News::select('id', 'thumbnail')
            ->where('id', $request->id)
            ->get();

        $storagePath = public_path('upload/files/');
        $path = parse_url($news[0]->thumbnail, PHP_URL_PATH);
        $file_name = basename($path);
        $fileUploadLink = $storagePath . 'news/' . $file_name;

        if ($news[0]->thumbnail !== '' && $news[0]->thumbnail !== null) {
            if (file_exists($fileUploadLink)) {
                unlink($fileUploadLink);
            }
        }

        $delete = News::where('id', $request->id)->delete();
        if ($delete) {
            $success = ['status' => 'success', 'message' => 'deleted successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'delete failed'];
        }

        return response()->json($success, $this->successStatus);
    }
}
