<?php

namespace App\Http\Controllers;
// namespace App\Http\Controllers\Response;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Audio;
use App\Classes\VideoStream;
use Illuminate\Support\Facades\Response;
use File;
use Carbon\Carbon;
// use App\Classes\MP3File;

use function GuzzleHttp\json_decode;
// use wapmorgan\Mp3Info\Mp3Info;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AudioController extends Controller
{
    //

    public $successStatus = 200;

    public function listAllByUser(Request $request)
    {
        $audio = Audio::select(
            'id',
            'audio',
            // 'audio_id',
            'local_path',
            'artist',
            'title',
            'file_name',
            'mime_type',
            'duration',
            'size'
        )
            ->where(function ($query) use ($request) {
                $query->where('artist', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%')
                    ->orWhere('mime_type', 'like', '%' . $request->search . '%')
                    ->orWhere('file_name', 'like', '%' . $request->search . '%');
            })
            ->where('status_delete', '=', 0)
            ->offset($request->offset)
            ->limit($request->limit)
            ->get();

        $audioCount = Audio::select(
            'id',
            'audio'
        )

            ->where(function ($query) use ($request) {
                $query->where('artist', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%')
                    ->orWhere('mime_type', 'like', '%' . $request->search . '%')
                    ->orWhere('file_name', 'like', '%' . $request->search . '%');
            })
            ->where('status_delete', '=', 0)
            ->count();

        $arr = [];

        // $audio['audioBinary'] = $audioBinary;
        $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

        foreach ($audio as $object) {

            // $object['location'] = storage_path() . '\public\audio\3\f4ARrUp9Y91p27N9bHv5udbyftr1xQugtH53e3isxSBQx79NOI190911095503.mp3';
            // $object['audioBinary'] = new BinaryFileResponse($object['location']);
            // BinaryFileResponse::trustXSendfileTypeHeader();

            $arr[] = $object->toArray();

            // $arr['audioBinary'] =

        }
        return response()->json(['data' => $arr, 'total' => $audioCount, 'audio' => $audio]);
    }

    public function listenAudio($public, $audio, $user_id, $audio_id)
    {
        $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
        $filename = $storagePath . $public . '/' . $audio . '/' . $user_id . '/' . $audio_id;

        $stream = new VideoStream($filename);
        $stream->start();

        // $filestream = new \App\Http\Responses\S3FileStream($filename, 'public');
        // return $filestream->output();

        // return $this->streamFile("audio/mpeg", $filename);

        // $filesize = (int) File::size($filename);

        // $file = File::get($filename);

        // $response = Response::make($file, 200);
        // $response->header('Content-Type', 'audio/mpeg');
        // $response->header('Content-Length', $filesize);
        // $response->header('Accept-Ranges', 'bytes');
        // $response->header('Content-Range', 'bytes 0-'.$filesize.'/'.$filesize);

        // return $response;

        // $mime_type = "audio/mpeg";
        // $headers = array(
        //     'Accept-Ranges: 0-' . (filesize($file) -1) ,

        //     'Content-Length:'.filesize($file),
        //     'Content-Type:' . $mime_type,
        //     'Content-Disposition: inline; filename="'.'X0PocKJd0JGXG1kskAcC2muSm0te9fxlSHyrvWNh50hVPmapbV190911091049' . '.mp3'.'"'

        // );
        // $fileContents = File::get($file);
        // return Response::make($fileContents, 200, $headers);

        // return $path;
        // $user = \Auth::user();
        // if ($user->activated_at) {
        // $response = new BinaryFileResponse($path);
        // BinaryFileResponse::trustXSendfileTypeHeader();

        // return $response;
        // }
        // \App::abort(400);

        // $filesize = (int) File::size($filename);

        // $file = File::get($filename);

        // $response = Response::make($file, 200);
        // $response->header('Content-Type', 'audio/mpeg');
        // $response->header('Content-Length', $filesize);
        // $response->header('Accept-Ranges', 'bytes');
        // $response->header('Content-Range', 'bytes 0-'.$filesize.'/'.$filesize);

        // return $response;

        // return (new Response($file, 200))
        // ->header('Content-Type', 'audio/mpeg');
    }

    // Provide a streaming file with support for scrubbing
    // private function streamFile( $contentType, $path ) {
    // 	$fullsize = filesize($path);
    // 	$size = $fullsize;
    // 	$stream = fopen($path, "r");
    // 	$response_code = 200;
    // 	$headers = array("Content-type" => $contentType);

    // 	// Check for request for part of the stream
    // 	$range = \Request::header('Range');
    // 	if($range != null) {
    // 		$eqPos = strpos($range, "=");
    // 		$toPos = strpos($range, "-");
    // 		$unit = substr($range, 0, $eqPos);
    // 		$start = intval(substr($range, $eqPos+1, $toPos));
    // 		$success = fseek($stream, $start);
    // 		if($success == 0) {
    // 			$size = $fullsize - $start;
    // 			$response_code = 206;
    // 			$headers["Accept-Ranges"] = $unit;
    // 			$headers["Content-Range"] = $unit . " " . $start . "-" . ($fullsize-1) . "/" . $fullsize;
    // 		}
    // 	}

    // 	$headers["Content-Length"] = $size;

    // 	return Response::stream(function () use ($stream) {
    // 		fpassthru($stream);
    // 	}, $response_code, $headers);
    // }

    public function storeUploadedAudio(Request $request)
    {
        // $request = \json_decode($request->all());

        // $requestData = json_decode(urldecode(file_get_contents('php://input')));

        // return response()->json($request->all());

        $validator = Validator::make($request->all(), [
            // 'audio' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac'
            // 'audio' => 'required|mimes:mpga,wav'
            'audio' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // BEGIN STORE

        // $storagePath = public_path('upload/files/');
        // $audioPath = public_path('upload/files/audio/');
        // $uploadPath = public_path('upload/');

        $host  = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');
        $random = str_random(50);
        $title = $random . $waktu;

        // return response()->json(['status' => 'success', 'title' => $title], $this->successStatus);

        $user = User::where('id', $request->user_id)->first();
        // $user = false;

        if ($user) {
            // if (!file_exists($uploadPath)) {
            //     mkdir($uploadPath);
            // }

            // if (!file_exists($storagePath)) {
            //     mkdir($storagePath);
            // }

            // if (!file_exists($audioPath)) {
            //     mkdir($audioPath);
            // }

            // if (!file_exists($audioPath . $request->user_id)) {
            //     mkdir($audioPath . $request->user_id);
            // }

            //AUDIO
            if ($request->audio !== '' && $request->audio !== null) {

                $audioBase64 = $request->audio;
                $audio = base64_decode($audioBase64);
                $original_name = $request->audio_name;
                $size  = $request->audio_size;
                $extension = $request->audio_ext;
                $mime_type = $request->audio_type;

                $path = 'public/audio/' . $request->user_id . '/' . $title . '.' . $extension;
                $storeResult = Storage::disk('local')->put($path, $audio);
                $fileAudioUrl = $protocol . '://' . $host . '/api/audio-listen/public/audio/' . $request->user_id . '/' . $title . '.' . $extension;

                $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
                $filename = $storagePath . $path;

                $audioDuration = $request->audio_duration;

                // $audio_info = new MP3File($filename);
                // $audioDuration = $audio_info->getDurationEstimate(); // duration in

                // $uniqueid = uniqid();
                // $filename = Carbon::now()->format('Ymd') . '_' . $uniqueid . '.' . $extension;
                // $fileAudioLink = $audioPath . $request->user_id . '/' . $title . '.' . $extension;
                // $audiopath = 'public/upload/files/audio/'  . $request->user_id . '/' . $title. '.' . $extension;
                // $file = $request->file('audio');
                // $path = $file->storeAs('public/upload/files/cdaudio', $file);
                // $request->audio->store('public');
                // $all_audios = $audiopath;

                // $file = new File($request->file('audio'));
                // $uploaded_file = $request->file('audio');
                // $original_name = $request->file('audio')->getClientOriginalName();
                // $size = $request->file('audio')->getSize();
                // $extension = $request->file('audio')->getClientOriginalExtension();

                // $path = $uploaded_file->store('audio');
                // $path = Storage::putFileAs('public/audio/' . $request->user_id, $uploaded_file, $title . '.' . $extension);

                // $filename = Carbon::now()->format('Ymd') . '_' . $uniqueid . '.' . $extension;
                // Automatically generate a unique ID for file name...
                // $path = Storage::putFile('audio', new File($original_name . $extension));

                // $audiopath = url('/storage/upload/files/audio/' . $filename);
                // $path = $uploaded_file->storeAs('public/upload/files/audio/', $filename);
                // $uploaded_file->move('public/upload/files/audio/', $filename);
                // $all_audios = $audiopath;

                // file_put_contents($fileAudioLink, $file);

                // $audioDuration = '';
            }

            // IMAGE
            // if ($request->audio !== '' && $request->audio !== null) {
            //     // $image_parts = explode(";base64,", $request->audio);
            //     // $image_type_aux = explode("image/", $image_parts[0]);
            //     // $image_type = $image_type_aux[1];
            //     // $image_base64 = base64_decode($image_parts[1]);

            //     $original_name = $request->file('audio')->getClientOriginalName();
            //     $size = $request->file('audio')->getSize();
            //     $extension = $request->file('audio')->getClientOriginalExtension();

            //     $filename = Carbon::now()->format('Ymd') . '_' . $uniqueid . '.' . $extension;
            //     $audiopath = url('/storage/upload/files/audio/' . $filename);
            //     $path = $file->storeAs('public/upload/files/audio/', $filename);
            //     $all_audios = $audiopath;

            //     $fileAudioLink = $audioPath . $request->user_id . '/' . $title . '.' . $extension;
            //     $fileAudioUrl = $protocol . '://' . $domain . '/' . $audioPath . $request->user_id . '/' . $title . '.' . $extension;
            //     file_put_contents($fileAudioLink, $image_base64);
            // } else {
            //     $fileAudioLink = '';
            //     $fileAudioUrl = '';
            // }

            $input = $request->all();

            // $data = new Audio;
            // $data->audio = $fileAudioUrl;
            // $data->local_path = $path;
            // $data->user_id = $request->user_id;
            // $data->artist = $request->artist;
            // $data->tilte = $request->title;
            // $data->file_name = $original_name;
            // $data->mime_type = $mime_type;
            // $data->duration = $audioDuration;
            // $data->size = $size;

            $input['audio'] = $fileAudioUrl;
            // $input['audio_id'] = $fileAudioUrl;
            $input['local_path'] = $path;
            $input['user_id'] = $request->user_id;
            $input['artist'] = $request->artist;
            $input['title'] = $request->title;
            $input['file_name'] = $original_name;
            $input['mime_type'] = $mime_type;
            $input['duration'] = $audioDuration;
            $input['size'] = $size;

            $id = Audio::create($input)->id;

            if ($id) {
                $input['id'] = $id;
                return response()->json(['status' => 'success', 'message' => 'created successfully', 'result' => $input/* , 'request' => $request->all(), 'user' => $user */], $this->successStatus);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'create failed'/* , 'request' => $request->all(), 'user' => $request->user */], 401);
            }
        } else {
            // $original_name = $request->file('audio')->getClientOriginalName();
            // $size = $request->file('audio')->getSize();
            // $extension = $request->file('audio')->getClientOriginalExtension();
            // $options = $request->options;

            return response()->json(['status' => 'failed', 'message' => 'user not found'/* , 'request' => $request->all(), 'user' => $user */], 200);
        }
    }

    public function deleteAudio(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'id' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 401);
        // }

        $update = Audio::where('id', $request->audio_id)->update(
            [
                'status_delete' => 1
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'deleted successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'delete failed'], 401);
        }

        // $arr = json_decode($request->id, true);

        // for ($i = 0; $i < sizeof($arr); $i++) {
        //     $update = Gallery::where('id', $arr[$i]['id'])->update(
        //         [
        //             'status_delete' => 1
        //         ]
        //     );

        //     if ($update) {
        //     } else {
        //         return response()->json(['status' => 'failed', 'message' => 'delete failed'], 401);
        //     }
        // }
        // return response()->json(['status' => 'success', 'message' => 'deleted successfully'], 200);
    }
}
