<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Car;
use Illuminate\Support\Facades\Validator;
use App\EventMember;
use App\EventMemberClass;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class CarController extends Controller
{
    public $successStatus = 200;

    public function listJudgedCarByJudgeId(Request $request)
    {
        $car = Car::select(
            'cars.avatar AS avatar',
            'cars.id AS car_id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'engine',
            'power',
            'seat',
            'transmission_type',
            'vehicle',
            'license_plate',
            'vin_number',
            'type',
            'color',
            'headunits',
            'processor',
            'power_amplifier',
            'speakers',
            'wires',
            'other_devices',
            'front_car_image',
            'cars.user_id'
        )

            ->leftJoin('event_member_classes', 'event_member_classes.car_id', '=', 'cars.id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('events', 'events.id', '=', 'event_judges.event_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'cars.user_id')
            ->whereNotNull('event_member_classes.id')
            ->whereNotNull('event_member_classes.grand_total')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('event_judge_activities.id')
            ->where('events.status_score_final', '=', 1)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->distinct()
            ->get();


        $carCount = Car::select(
            'cars.avatar AS avatar',
            'cars.id AS car_id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'engine',
            'power',
            'seat',
            'transmission_type',
            'vehicle',
            'license_plate',
            'vin_number',
            'type',
            'color',
            'headunits',
            'processor',
            'power_amplifier',
            'speakers',
            'wires',
            'other_devices',
            'front_car_image',
            'cars.user_id'
        )

            ->leftJoin('event_member_classes', 'event_member_classes.car_id', '=', 'cars.id')
            ->leftJoin('event_judge_member_assignments', 'event_judge_member_assignments.event_member_class_id', '=', 'event_member_classes.id')
            ->leftJoin('event_judge_activities', 'event_judge_activities.id', '=', 'event_judge_member_assignments.event_judge_activity_id')
            ->leftJoin('event_judges', 'event_judges.id', '=', 'event_judge_activities.event_judge_id')
            ->leftJoin('events', 'events.id', '=', 'event_judges.event_id')
            ->leftJoin('event_members', 'event_members.id', '=', 'event_member_classes.event_member_id')
            ->leftJoin('users', 'users.id', '=', 'cars.user_id')
            ->whereNotNull('event_member_classes.id')
            ->whereNotNull('event_member_classes.grand_total')
            ->whereNotNull('event_judge_member_assignments.id')
            ->whereNotNull('event_judge_activities.id')
            ->where('events.status_score_final', '=', 1)
            ->where('event_judges.judge_id', '=', $request->judge_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%');
            })
            ->distinct('cars.id')
            ->count('cars.id');

        $arr = [];

        foreach ($car as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $carCount]);
    }

    public function listAllLimit(Request $request)
    {
        $car = Car::select(
            'avatar',
            'cars.id AS id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'engine',
            'power',
            'seat',
            'transmission_type',
            'vehicle',
            'license_plate',
            'vin_number',
            'type',
            'color',
            'headunits',
            'processor',
            'power_amplifier',
            'speakers',
            'wires',
            'other_devices',
            'front_car_image',
            'user_id',
            'cars.created_at'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('vehicle', 'like', '%' . $request->search . '%')
                    ->orWhere('license_plate', 'like', '%' . $request->search . '%')
                    ->orWhere('color', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('cars.created_at', 'desc')
            ->get();

        $carCount = Car::select(
            'cars.id AS id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'vehicle AS car',
            'license_plate',
            'color'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('vehicle', 'like', '%' . $request->search . '%')
                    ->orWhere('license_plate', 'like', '%' . $request->search . '%')
                    ->orWhere('color', 'like', '%' . $request->search . '%');
            })
            ->count();

        $arr = [];

        foreach ($car as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $carCount]);
    }

    public function listAllLimitOrder(Request $request)
    {
        $car = Car::select(
            'avatar',
            'cars.id AS id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'engine',
            'power',
            'seat',
            'transmission_type',
            'vehicle',
            'license_plate',
            'color',
            'headunits',
            'processor',
            'power_amplifier',
            'speakers',
            'wires',
            'other_devices',
            'front_car_image',
            'cars.user_id'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('vehicle', 'like', '%' . $request->search . '%')
                    ->orWhere('license_plate', 'like', '%' . $request->search . '%')
                    ->orWhere('color', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $carCount = Car::select(
            'cars.id AS id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'vehicle AS car',
            'license_plate',
            'color'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('vehicle', 'like', '%' . $request->search . '%')
                    ->orWhere('license_plate', 'like', '%' . $request->search . '%')
                    ->orWhere('color', 'like', '%' . $request->search . '%');
            })
            ->get()->count();

        $arr = [];

        foreach ($car as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $carCount]);
    }

    public function listAllByIdUserLimit(Request $request)
    {
        $car = Car::select(
            'avatar',
            'cars.id AS id',
            'users.name AS owner',
            'engine',
            'power',
            'seat',
            'transmission_type',
            'vehicle',
            'license_plate',
            'vin_number',
            'type',
            'color',
            'headunits',
            'processor',
            'power_amplifier',
            'speakers',
            'wires',
            'other_devices',
            'front_car_image',
            'signal_flowchart',
            'power_supply_flowchart'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where('cars.user_id', '=', $request->user_id)
            ->where(function ($query) use ($request) {
                $query->where('users.name', 'like', '%' . $request->search . '%')
                    ->orWhere('vehicle', 'like', '%' . $request->search . '%')
                    ->orWhere('license_plate', 'like', '%' . $request->search . '%')
                    ->orWhere('color', 'like', '%' . $request->search . '%');
            })
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $carCount = Car::select(
            'cars.id AS id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'vehicle AS car',
            'license_plate',
            'color'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where('cars.user_id', '=', $request->user_id)
            ->get()->count();

        $arr = [];

        foreach ($car as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $carCount]);
    }

    public function listAllByIdUserLimitOrder(Request $request)
    {
        $car = Car::select(
            'avatar',
            'cars.id AS id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'engine',
            'power',
            'seat',
            'transmission_type',
            'vehicle',
            'license_plate',
            'color',
            'headunits',
            'processor',
            'power_amplifier',
            'speakers',
            'wires',
            'other_devices',
            'front_car_image'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where('cars.user_id', '=', $request->user_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $carCount = Car::select(
            'cars.id AS id',
            'users.name AS owner',
            'users.manual_input AS manual_input',
            'vehicle AS car',
            'license_plate',
            'color'
        )
            ->join('users', 'users.id', '=', 'cars.user_id')
            ->where('cars.user_id', '=', $request->user_id)
            ->get()->count();

        $arr = [];

        foreach ($car as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $carCount]);
    }

    public function listDetailByCarId(Request $request)
    {
        $car = Car::where('id', $request->id)->first();
        return response()->json($car, 200);
    }

    public function listTimeline(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        $eventMember = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location',
            'events.id AS event_id'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('date_start', '>', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_member_classes.car_id', '=', $request->car_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->distinct()
            ->get();

        $eventMemberCount = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('date_start', '>', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_member_classes.car_id', '=', $request->car_id)
            ->distinct()
            ->get()
            ->count();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listTimelineOrder(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        $eventMember = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location',
            'events.id AS event_id'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->where('date_start', '>', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_members.car_id', '=', $request->car_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventMemberCount = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->where('date_start', '>', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_members.car_id', '=', $request->car_id)
            ->get()->count();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listParticipatedEvents(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        $eventMember = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location',
            'events.id AS event_id'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('date_start', '<=', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_member_classes.car_id', '=', $request->car_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->distinct()
            ->get();

        $eventMemberCount = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->join('event_member_classes', 'event_member_classes.event_member_id', '=', 'event_members.id')
            ->where('date_start', '<=', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_member_classes.car_id', '=', $request->car_id)
            ->distinct()
            ->get()
            ->count();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
    }

    public function listParticipatedEventsOrder(Request $request)
    {
        $date = date('Y-m-d');
        $time = date('H:i');

        $eventMember = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location',
            'events.id AS event_id'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->where('date_start', '<=', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_members.car_id', '=', $request->car_id)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy($request->column, $request->sort)
            ->get();

        $eventMemberCount = EventMember::select(
            'events.title AS event_title',
            'events.date_start AS date_start',
            'events.date_end AS date_end',
            'events.time_start AS time_start',
            'events.time_end AS time_end',
            'events.location AS location'
        )
            ->join('events', 'events.id', '=', 'event_members.event_id')
            ->where('date_start', '<=', $date)
            ->where('event_members.member_id', '=', $request->user_id)
            ->where('event_members.car_id', '=', $request->car_id)
            ->get()->count();

        $arr = [];

        foreach ($eventMember as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $eventMemberCount]);
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
            // 'engine' => 'required',
            // 'power' => 'required',
            // 'seat' => 'required',
            // 'transmission_type' => 'required',
            'vehicle' => 'required',
            'license_plate' => 'required|unique:cars',
            'type' => 'required',
            'color' => 'required',
            'headunits' => 'required',
            'processor' => 'required',
            'power_amplifier' => 'required',
            'speakers' => 'required',
            'wires' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // BEGIN STORE

        $storagePath = public_path('upload/files/');
        $carPath = public_path('upload/files/car/');
        $uploadPath = public_path('upload/');

        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');

        $user = User::where('id', $request->user_id)->first();

        $avatarNameArr = explode(' ', $user->name);
        $avatarName = implode('-', $avatarNameArr);

        $frontCarNameArr = explode(' ', $request->license_plate);
        $frontCarName = implode('-', $frontCarNameArr);

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath);
        }

        if (!file_exists($storagePath)) {
            mkdir($storagePath);
        }

        if (!file_exists($carPath)) {
            mkdir($carPath);
        }

        if (!file_exists($carPath . $request->user_id)) {
            mkdir($carPath . $request->user_id);
        }

        // // AVATAR
        // if ($request->avatar !== '' && $request->avatar !== null) {
        //     // $image_parts = explode(";base64,", $request->avatar);
        //     // $image_type_aux = explode("image/", $image_parts[0]);
        //     // $image_type = $image_type_aux[1];
        //     // $image_base64 = base64_decode($image_parts[1]);
        //     // $fileAvatarLink = $carPath . $request->user_id . '/' . $avatarName . $waktu . '.' . $image_type;

        //     // STORE IMAGE
        //     $base64_image = $request->avatar; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $avatarName . $waktu . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $fileAvatarUrl = $protocol . '://' . $host . '/public' . $storageURL;

        //     // $fileAvatarUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/' . $avatarName . $waktu . '.' . $image_type;
        //     // file_put_contents($fileAvatarLink, $image_base64);
        // } else {
        //     $fileAvatarLink = '';
        //     $fileAvatarUrl = '';
        // }

        // AVATAR
        if ($request->avatar !== '' && $request->avatar !== null) {
            // STORE IMAGE
            $base64_image = $request->avatar; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $path = 'car/' . $user->id . '/' . $avatarName . $waktu . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($path, base64_decode($file_data));

            // Construct the public URL for the saved file
            $fileAvatarUrl = $protocol . '://' . $host . '/public/storage/' . $path;
        } else {
            $fileAvatarLink = '';
            $fileAvatarUrl = '';
        }


        // // FRONT-CAR-IMAGE
        // if ($request->front_car_image !== '' && $request->front_car_image !== null) {
        //     // $image_parts = explode(";base64,", $request->front_car_image);
        //     // $image_type_aux = explode("image/", $image_parts[0]);
        //     // $image_type = $image_type_aux[1];
        //     // $image_base64 = base64_decode($image_parts[1]);
        //     // $fileFrontLink = $carPath . $request->user_id . '/' . $frontCarName . $waktu . '.' . $image_type;
        //     // $fileFrontUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/' . $frontCarName . $waktu . '.' . $image_type;
        //     // file_put_contents($fileFrontLink, $image_base64);

        //     // STORE IMAGE
        //     $base64_image = $request->front_car_image; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $frontCarName . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $fileFrontUrl = $protocol . '://' . $host . '/public' . $storageURL;
        // } else {
        //     $fileFrontLink = '';
        //     $fileFrontUrl = '';
        // }

        // FRONT-CAR-IMAGE
        if ($request->front_car_image !== '' && $request->front_car_image !== null) {
            // STORE IMAGE
            $base64_image = $request->front_car_image; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $path = 'car/' . $user->id . '/' . $frontCarName . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($path, base64_decode($file_data));

            // Construct the public URL for the saved file
            $fileFrontUrl = $protocol . '://' . $host . '/public/storage/' . $path;
        } else {
            $fileFrontLink = '';
            $fileFrontUrl = '';
        }

        // // SIGNAL-FLOWCHART
        // if ($request->signal_flowchart !== '' && $request->signal_flowchart !== null) {
        //     // $image_parts = explode(";base64,", $request->signal_flowchart);
        //     // $image_type_aux = explode("image/", $image_parts[0]);
        //     // $image_type = $image_type_aux[1];
        //     // $image_base64 = base64_decode($image_parts[1]);
        //     // $fileSignalLink = $carPath . $request->user_id . '/signal-' . $frontCarName . $waktu . '.' . $image_type;
        //     // $fileSignalUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/signal-' . $frontCarName . $waktu . '.' . $image_type;
        //     // file_put_contents($fileSignalLink, $image_base64);

        //     // STORE IMAGE
        //     $base64_image = $request->signal_flowchart; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $fileSignalUrl = $protocol . '://' . $host . '/public' . $storageURL;
        // } else {
        //     $fileSignalLink = '';
        //     $fileSignalUrl = '';
        // }

        // SIGNAL-FLOWCHART
        if ($request->signal_flowchart !== '' && $request->signal_flowchart !== null) {
            // STORE IMAGE
            $base64_image = $request->signal_flowchart; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $path = 'car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($path, base64_decode($file_data));

            // Construct the public URL for the saved file
            $fileSignalUrl = $protocol . '://' . $host . '/public/storage/' . $path;
        } else {
            $fileSignalLink = '';
            $fileSignalUrl = '';
        }

        // // POWER-SUPPLY-FLOW
        // if ($request->power_supply_flowchart !== '' && $request->power_supply_flowchart !== null) {
        //     // $image_parts = explode(";base64,", $request->power_supply_flowchart);
        //     // $image_type_aux = explode("image/", $image_parts[0]);
        //     // $image_type = $image_type_aux[1];
        //     // $image_base64 = base64_decode($image_parts[1]);
        //     // $filePowerLink = $carPath . $request->user_id . '/power-' . $frontCarName . $waktu . '.' . $image_type;
        //     // $filePowerUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/power-' . $frontCarName . $waktu . '.' . $image_type;
        //     // file_put_contents($filePowerLink, $image_base64);

        //     // STORE IMAGE
        //     $base64_image = $request->power_supply_flowchart; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $filePowerUrl = $protocol . '://' . $host . '/public' . $storageURL;
        // } else {
        //     $filePowerLink = '';
        //     $filePowerUrl = '';
        // }

        // POWER-SUPPLY-FLOW
        if ($request->power_supply_flowchart !== '' && $request->power_supply_flowchart !== null) {
            // STORE IMAGE
            $base64_image = $request->power_supply_flowchart; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $path = 'car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($path, base64_decode($file_data));

            // Construct the public URL for the saved file
            $filePowerUrl = $protocol . '://' . $host . '/public/storage/' . $path;
        } else {
            $filePowerLink = '';
            $filePowerUrl = '';
        }

        $input = $request->all();

        $input['avatar'] = $fileAvatarUrl;
        $input['front_car_image'] = $fileFrontUrl;
        $input['signal_flowchart'] = $fileSignalUrl;
        $input['power_supply_flowchart'] = $filePowerUrl;
        $input['vin_number'] = $input['vin_number'] ?? "";

        $saveCar = Car::create($input);

        if ($saveCar) {
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
    public function update(Request $request, Car $car)
    {
        $car = Car::select('id', 'license_plate')
            ->where('license_plate', $request->license_plate)
            ->get();

        $carVin = Car::select('id', 'vin_number')
            ->where('vin_number', $request->vin_number)
            ->get();

        $carOld = Car::select(
            'id',
            'user_id',
            'avatar',
            'engine',
            'power',
            'seat',
            'transmission_type',
            'vehicle',
            'license_plate',
            'vin_number',
            'type',
            'color',
            'front_car_image',
            'headunits',
            'processor',
            'power_amplifier',
            'speakers',
            'wires',
            'other_devices',
            'signal_flowchart',
            'power_supply_flowchart'
        )
            ->where('id', $request->id)
            ->get();

        $count = $car->count();
        $countVin = $request->vin_number == "" ? 0 : $carVin->count();

        // return response()->json(['status' => 'failed', 'message' => 'license plate have been used', 'car' => $car, 'carVin' => $carVin], 200);

        if ($count > 0) {
            if ($carOld[0]->id !== $car[0]->id) {
                return response()->json(['status' => 'failed', 'message' => 'license plate have been used'], 200);
            } else {
                return $this->updateCar($request, $carOld);
            }
        } else if ($countVin > 0) {
            if ($carOld[0]->id !== $carVin[0]->id) {
                return response()->json(['status' => 'failed', 'message' => 'vin number have been used'], 200);
            } else {
                return $this->updateCar($request, $carOld);
            }
        } else {
            return $this->updateCar($request, $carOld);
        }
    }

    protected function updateCar(Request $request, $carOld)
    {
        foreach ($carOld as $carDetail) {
            $carAvatar = $carDetail->avatar;
            $carSignalFlowChart = $carDetail->signal_flowchart;
            $carPowerSupplyFlowChart = $carDetail->power_supply_flowchart;
        }
        $storagePath = public_path('upload/files/');
        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $carPath = public_path('upload/files/car/');

        $waktu = date('ymdHis');

        $user = User::where('id', $request->user_id)->first();

        $avatarNameArr = explode(' ', $user->name);
        $avatarName = implode('-', $avatarNameArr);

        $frontCarNameArr = explode(' ', $request->license_plate);
        $frontCarName = implode('-', $frontCarNameArr);

        // // AVATAR
        // if ($request->avatar !== '' && $request->avatar !== null) {

        //     // STORE IMAGE
        //     $base64_image = $request->avatar; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $avatarName . $waktu . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $fileAvatarUrl = $protocol . '://' . $host . '/public' . $storageURL;
        // } else {
        //     $fileAvatarLink = '';
        //     $fileAvatarUrl = '';
        // }

        // AVATAR
        if ($request->avatar !== '' && $request->avatar !== null && $request->avatar !== $carAvatar) {
            // STORE IMAGE
            $base64_image = $request->avatar; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $path = 'car/' . $user->id . '/' . $avatarName . $waktu . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($path, base64_decode($file_data));

            // Construct the public URL for the saved file
            $fileAvatarUrl = $protocol . '://' . $host . '/public/storage/' . $path;
        } else if ($request->avatar !== '' && $request->avatar !== null && $request->avatar === $carAvatar) {
            $fileAvatarUrl = $carAvatar;
        } else {
            $fileAvatarLink = '';
            $fileAvatarUrl = '';
        }


        // // FRONT-CAR-IMAGE
        // if ($request->front_car_image !== '' && $request->front_car_image !== null) {

        //     // STORE IMAGE
        //     $base64_image = $request->front_car_image; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $frontCarName . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $fileFrontUrl = $protocol . '://' . $host . '/public' . $storageURL;
        // } else {
        //     $fileFrontLink = '';
        //     $fileFrontUrl = '';
        // }

        // FRONT-CAR-IMAGE
        if ($request->front_car_image !== '' && $request->front_car_image !== null) {
            // STORE IMAGE
            $base64_image = $request->front_car_image; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $path = 'car/' . $user->id . '/' . $frontCarName . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($path, base64_decode($file_data));

            // Construct the public URL for the saved file
            $fileFrontUrl = $protocol . '://' . $host . '/public/storage/' . $path;
        } else {
            $fileFrontLink = '';
            $fileFrontUrl = '';
        }


        // // SIGNAL-FLOWCHART
        // if ($request->signal_flowchart !== '' && $request->signal_flowchart !== null) {

        //     // STORE IMAGE
        //     $base64_image = $request->signal_flowchart; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $fileSignalUrl = $protocol . '://' . $host . '/public' . $storageURL;
        // } else {
        //     $fileSignalLink = '';
        //     $fileSignalUrl = '';
        // }

        // SIGNAL-FLOWCHART
        if ($request->signal_flowchart !== '' && $request->signal_flowchart !== null && $request->signal_flowchart !== $carSignalFlowChart) {
            // STORE IMAGE
            $base64_image = $request->signal_flowchart; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $publicPath = 'car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($publicPath, base64_decode($file_data));

            // Construct the public URL for the saved file
            $fileSignalUrl = $protocol . '://' . $host . '/public' . Storage::url($publicPath);
        } else if ($request->signal_flowchart !== '' && $request->signal_flowchart !== null && $request->signal_flowchart === $carSignalFlowChart) {
            $fileSignalUrl = $carSignalFlowChart;
        } else {
            $fileSignalLink = '';
            $fileSignalUrl = '';
        }

        // // POWER-SUPPLY-FLOW
        // if ($request->power_supply_flowchart !== '' && $request->power_supply_flowchart !== null) {

        //     // STORE IMAGE
        //     $base64_image = $request->power_supply_flowchart; // your base64 encoded
        //     @list($type, $file_data) = explode(';', $base64_image);
        //     @list(, $file_data) = explode(',', $file_data);

        //     $path = 'public/car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';
        //     Storage::disk('local')->put($path, base64_decode($file_data));
        //     $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        //     $storageURL = Storage::url($path);

        //     $filePowerUrl = $protocol . '://' . $host . '/public' . $storageURL;
        // } else {
        //     $filePowerLink = '';
        //     $filePowerUrl = '';
        // }

        // POWER-SUPPLY-FLOW
        if ($request->power_supply_flowchart !== '' && $request->power_supply_flowchart !== null && $request->power_supply_flowchart !== $carPowerSupplyFlowChart) {
            // STORE IMAGE
            $base64_image = $request->power_supply_flowchart; // your base64 encoded
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);

            // Define path for storage directory
            $path = 'car/' . $user->id . '/' . $frontCarName . str_random(10) . '.png';

            // Upload the image directly to the 'public2' disk
            Storage::disk('public2')->put($path, base64_decode($file_data));

            // Construct the public URL for the saved file
            $filePowerUrl = $protocol . '://' . $host . '/public/storage/' . $path;
        } else if ($request->power_supply_flowchart !== '' && $request->power_supply_flowchart !== null && $request->power_supply_flowchart === $carPowerSupplyFlowChart) {
            $filePowerUrl = $carPowerSupplyFlowChart;
        } else {
            $filePowerLink = '';
            $filePowerUrl = '';
        }


        // $cekAvatar = substr($request['avatar'], 0, 10);
        // $cekFrontImage = substr($request['front_car_image'], 0, 10);
        // $cekSignalImage = substr($request['signal_flowchart'], 0, 10);
        // $cekPowerImage = substr($request['power_supply_flowchart'], 0, 10);

        // // AVATAR
        // if ($cekAvatar === 'data:image') {
        //     if ($carOld[0]->avatar !== '' && $carOld[0]->avatar !== null) {
        //         $path = parse_url($carOld[0]->avatar, PHP_URL_PATH);
        //         $file_name = basename($path);
        //         $fileAvatarLink = $carPath . $request->user_id . '/' . $file_name;

        //         if (file_exists($fileAvatarLink)) {
        //             unlink($fileAvatarLink);
        //         }
        //     }
        //     $image_parts = explode(";base64,", $request->avatar);
        //     $image_type_aux = explode("image/", $image_parts[0]);
        //     $image_type = $image_type_aux[1];
        //     $image_base64 = base64_decode($image_parts[1]);
        //     $fileAvatarLink = $carPath . $request->user_id . '/' . $avatarName . $waktu . '.' . $image_type;
        //     $fileAvatarUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/' . $avatarName . $waktu . '.' . $image_type;
        //     file_put_contents($fileAvatarLink, $image_base64);
        // } else {
        //     $fileAvatarUrl = $request['avatar'];
        // }

        // // FRONT-CAR-IMAGE
        // if ($cekFrontImage === 'data:image') {
        //     if ($carOld[0]->front_car_image !== '' && $carOld[0]->front_car_image !== null) {
        //         $path = parse_url($carOld[0]->front_car_image, PHP_URL_PATH);
        //         $file_name = basename($path);
        //         $fileFrontLink = $carPath . $request->user_id . '/' . $file_name;

        //         if (file_exists($fileFrontLink)) {
        //             unlink($fileFrontLink);
        //         }
        //     }
        //     $image_parts = explode(";base64,", $request->front_car_image);
        //     $image_type_aux = explode("image/", $image_parts[0]);
        //     $image_type = $image_type_aux[1];
        //     $image_base64 = base64_decode($image_parts[1]);
        //     $fileFrontLink = $carPath . $request->user_id . '/' . $frontCarName . $waktu . '.' . $image_type;
        //     $fileFrontUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/' . $frontCarName . $waktu . '.' . $image_type;
        //     file_put_contents($fileFrontLink, $image_base64);
        // } else {
        //     $fileFrontUrl = $request['front_car_image'];
        // }

        // // SIGNAL-FLOWCHART
        // if ($cekSignalImage === 'data:image') {
        //     if ($carOld[0]->signal_flowchart !== '' && $carOld[0]->signal_flowchart !== null) {
        //         $path = parse_url($carOld[0]->signal_flowchart, PHP_URL_PATH);
        //         $file_name = basename($path);
        //         $fileSignalLink = $carPath . $request->user_id . '/' . $file_name;

        //         if (file_exists($fileSignalLink)) {
        //             unlink($fileSignalLink);
        //         }
        //     }
        //     $image_parts = explode(";base64,", $request->signal_flowchart);
        //     $image_type_aux = explode("image/", $image_parts[0]);
        //     $image_type = $image_type_aux[1];
        //     $image_base64 = base64_decode($image_parts[1]);
        //     $fileSignalLink = $carPath . $request->user_id . '/signal-' . $frontCarName . $waktu . '.' . $image_type;
        //     $fileSignalUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/signal-' . $frontCarName . $waktu . '.' . $image_type;
        //     file_put_contents($fileSignalLink, $image_base64);
        // } else {
        //     $fileSignalUrl = $carOld[0]->signal_flowchart;
        // }

        // // POWER-SUPPLY-FLOWCHART
        // if ($cekPowerImage === 'data:image') {
        //     if ($carOld[0]->power_supply_flowchart !== '' && $carOld[0]->power_supply_flowchart !== null) {
        //         $path = parse_url($carOld[0]->power_supply_flowchart, PHP_URL_PATH);
        //         $file_name = basename($path);
        //         $filePowerLink = $carPath . $request->user_id . '/' . $file_name;

        //         if (file_exists($filePowerLink)) {
        //             unlink($filePowerLink);
        //         }
        //     }
        //     $image_parts = explode(";base64,", $request->power_supply_flowchart);
        //     $image_type_aux = explode("image/", $image_parts[0]);
        //     $image_type = $image_type_aux[1];
        //     $image_base64 = base64_decode($image_parts[1]);
        //     $filePowerLink = $carPath . $request->user_id . '/power-' . $frontCarName . $waktu . '.' . $image_type;
        //     $filePowerUrl = $protocol . '://' . $domain . '/upload/files/car/' . $request->user_id . '/power-' . $frontCarName . $waktu . '.' . $image_type;
        //     file_put_contents($filePowerLink, $image_base64);
        // } else {
        //     $filePowerUrl = $carOld[0]->power_supply_flowchart;
        // }

        $carUpdate['avatar'] = $fileAvatarUrl;
        $carUpdate['front_car_image'] = $fileFrontUrl;
        $carUpdate['signal_flowchart'] = $fileSignalUrl;
        $carUpdate['power_supply_flowchart'] = $filePowerUrl;

        // $carUpdate['engine'] = $request->engine;
        // $carUpdate['power'] = $request->power;
        // $carUpdate['seat'] = $request->seat;
        // $carUpdate['transmission_type'] = $request->transmission_type;
        $carUpdate['vehicle'] = $request->vehicle;
        $carUpdate['license_plate'] = $request->license_plate;
        $carUpdate['vin_number'] = $request->vin_number;
        $carUpdate['type'] = $request->type;
        $carUpdate['color'] = $request->color;
        $carUpdate['headunits'] = $request->headunits;
        $carUpdate['processor'] = $request->processor;
        $carUpdate['power_amplifier'] = $request->power_amplifier;
        $carUpdate['speakers'] = $request->speakers;
        $carUpdate['wires'] = $request->wires;
        $carUpdate['other_devices'] = $request->other_devices;

        $update = Car::where('id', $request->id)->update(
            [
                'avatar' => $carUpdate['avatar'],
                'front_car_image' => $carUpdate['front_car_image'],
                // 'engine' => $carUpdate['engine'],
                // 'power' => $carUpdate['power'],
                // 'seat' => $carUpdate['seat'],
                // 'transmission_type' => $carUpdate['transmission_type'],
                'vehicle' => $carUpdate['vehicle'],
                'license_plate' => $carUpdate['license_plate'],
                'vin_number' => $carUpdate['vin_number'],
                'type' => $carUpdate['type'],
                'color' => $carUpdate['color'],
                'headunits' => $carUpdate['headunits'],
                'processor' => $carUpdate['processor'],
                'power_amplifier' => $carUpdate['power_amplifier'],
                'speakers' => $carUpdate['speakers'],
                'wires' => $carUpdate['wires'],
                'other_devices' => $carUpdate['other_devices'],
                'signal_flowchart' => $carUpdate['signal_flowchart'],
                'power_supply_flowchart' => $carUpdate['power_supply_flowchart']
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    public function delete(Request $request, Car $car)
    {
        $eventMemberClass = EventMemberClass::select('id', 'car_id')
            ->where('car_id', $request->id)
            ->get();


        $countEventMemberClass = $eventMemberClass->count();
        if ($countEventMemberClass > 0) {
            return response()->json(['status' => 'failed', 'message' => 'can not delete car because car have been used in event'], 200);
        } else {
            $carPath = public_path('upload/files/car/');

            $car = Car::where('id', $request->id)->first();

            if ($car === null) {
                $success = ['status' => 'failed', 'message' => 'Car not found'];
                return response()->json($success, $this->successStatus);
            }

            $avatar = $car->avatar;
            $frontImage = $car->front_car_image;
            $signalImage = $car->signal_flowchart;
            $powerImage = $car->power_supply_flowchart;

            $pathAvatar = parse_url($avatar, PHP_URL_PATH);
            $fileNameAvatar = basename($pathAvatar);
            $fileAvatarLink = $carPath . $car->user_id . '/' . $fileNameAvatar;

            $pathFrontImage = parse_url($frontImage, PHP_URL_PATH);
            $fileNameFrontImage = basename($pathFrontImage);
            $fileFrontImageLink = $carPath . $car->user_id . '/' . $fileNameFrontImage;

            $pathSignalImage = parse_url($signalImage, PHP_URL_PATH);
            $fileNameSignalImage = basename($pathSignalImage);
            $fileSignalImageLink = $carPath . $car->user_id . '/' . $fileNameSignalImage;

            $pathPowerImage = parse_url($powerImage, PHP_URL_PATH);
            $fileNamePowerImage = basename($pathPowerImage);
            $filePowerImageLink = $carPath . $car->user_id . '/' . $fileNamePowerImage;

            $storage_path_car = storage_path('app/public/car/' . $car->user_id . '/');
            $car_id_path = 'car/' . $car->user_id . '/';

            Storage::disk('public2')->delete($car_id_path . $fileNameAvatar);
            Storage::disk('public2')->delete($car_id_path . $fileNameFrontImage);
            Storage::disk('public2')->delete($car_id_path . $fileNameSignalImage);
            Storage::disk('public2')->delete($car_id_path . $fileNamePowerImage);

            // Storage::delete($storage_path_car . $fileNameAvatar);
            // Storage::delete($storage_path_car . $fileNameFrontImage);   
            // Storage::delete($storage_path_car . $fileNameSignalImage);    
            // Storage::delete($storage_path_car . $fileNamePowerImage);  

            // if (file_exists($fileAvatarLink)) {
            //     if(unlink($fileAvatarLink)) {

            //     } else {
            //     }
            // }

            // if (file_exists($fileFrontImageLink)) {
            //     if(unlink($fileFrontImageLink)) {

            //     } else {
            //     }
            // }

            // if (file_exists($fileSignalImageLink)) {
            //     if(unlink($fileSignalImageLink)) {

            //     } else {
            //     }
            // }

            // if (file_exists($filePowerImageLink)) {
            //     if(unlink($filePowerImageLink)) {

            //     } else {
            //     }
            // }

            $delete = Car::where('id', $request->id)->delete();
            if ($delete) {
                $success = ['status' => 'success', 'message' => 'deleted successfully', 'path' => $storage_path_car . $fileNameAvatar];
            } else {
                $success = ['status' => 'failed', 'message' => 'delete failed'];
            }

            return response()->json($success, $this->successStatus);
        }
    }
}
