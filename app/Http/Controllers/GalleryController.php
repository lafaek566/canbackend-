<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Gallery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use function GuzzleHttp\json_decode;

class GalleryController extends Controller
{
    public $successStatus = 200;

    public function listAllByUser(Request $request)
    {
        $gallery = Gallery::select(
            'id',
            'image'
        )
            ->where('user_id', '=', $request->user_id)
            ->where('status_delete', '=', 0)
            ->offset($request->offset)
            ->limit($request->limit)
            ->orderBy('created_at', 'desc')
            ->get();

        $galleryCount = Gallery::select(
            'id',
            'image'
        )
            ->where('user_id', '=', $request->user_id)
            ->where('status_delete', '=', 0)
            ->count();

        $arr = [];

        foreach ($gallery as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json(['data' => $arr, 'total' => $galleryCount]);
    }

    public function storeUploadedPhoto(Request $request)
    {
        // start count how many uploaded
        // $uploadcount = 0;

        // $files = $request->file('image');

        // if ($request->hasFile('image')) {
        //     foreach ($files as $file) {
        //         // $file->store('users/' . $this->user->id . '/messages');
        //         $uploadcount++;
        //     }
        // }

        // $input = $request->all();
        // $images = array();
        // if ($files = $request->file('file')) {
        //     foreach ($files as $file) {
        //         $name = $file->getClientOriginalName();
        //         // $file->move('image', $name);
        //         $images[$uploadcount] = $name;
        //         $uploadcount++;
        //     }
        // }
        // $name = '';
        // $data = array();
        // if ($request->hasfile('image')) {
        //     foreach ($request->file('image') as $image) {
        //         $name = $image->getClientOriginalName();
        //         // $image->move(public_path().'/images/', $name);
        //         $data[] = $name;
        //         $uploadcount++;
        //     }
        // }

        // if ($request->hasFile('filename')) {
        //     $allowedfileExtension = ['pdf', 'jpg', 'png', 'docx'];
        //     $files = $request->file('filename');
        //     foreach ($files as $file) {
        //         $filename = $file->getClientOriginalName();
        //         $extension = $file->getClientOriginalExtension();
        //         $check = in_array($extension, $allowedfileExtension);
        //         $uploadcount++;
        //     }
        // }

        // return response()->json(['status' => 'success', 'message' => 'created successfully', 'uploadCount' => $uploadcount, 'hasFIle' => $hasFile], $this->successStatus);

        $validator = Validator::make($request->all(), [
            // 'image' => 'required'
            //'user_id' => 'required',
            //'filename' => 'required',
            'file.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }


        if (count($request->files) > 0) {

            // BEGIN STORE

            $storagePath = public_path('upload/files/');
            $galleryPath = public_path('storage/gallery/');
            $uploadPath = public_path('upload/');

            $host = \Config::get('project-config.project_host');
            $protocol = \Config::get('project-config.project_protocol');
            $domain = \Config::get('project-config.project_domain');

            $waktu = date('ymdHis');
            $random = str_random(50);
            $title = $random . $waktu;

            // return response()->json(['status' => 'success', 'title' => $title], $this->successStatus);

            $user = User::where('id', Auth::user()->id)->first();

            if ($user) {
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath);
                }

                if (!file_exists($storagePath)) {
                    mkdir($storagePath);
                }

                if (!file_exists($galleryPath)) {
                    mkdir($galleryPath);
                }

                if (!file_exists($galleryPath . Auth::user()->id)) {
                    mkdir($galleryPath . Auth::user()->id);
                }

                foreach ($request->files as $image) {
                    // $name = $image->getClientOriginalName();
                    // $image->move(public_path() . '/images/', $name);
                    // $data[] = $name;
                    $extension = $image->getClientOriginalExtension();

                    // $path = 'public/gallery/' . $request->user_id . '/' . $title . '.' . $extension;
                    $path = 'public/gallery/' . Auth::user()->id  . "/" . $title . "." . $extension;
                    $storagePath = Storage::disk('local')->put($path, file_get_contents($image));
                    // $storageGet = Storage::get($storagePath);
                    $url = Storage::url($storagePath);
                    // $path = 'public/gallery/' . $request->user_id;
                    // $storeResult = $image->store($path);
                    // $finalPath = \str_replace('public', 'storage', $storeResult);
                    $fileImageUrl =  $protocol . '://'  . $host . '/storage/app/public/gallery/' . Auth::user()->id . "/" . $title  . "." . $extension;

                    // $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

                    // $fileImageLink = $galleryPath . $request->user_id . '/' . $title . '.' . $extension;
                    // $fileImageUrl = $protocol . '://' . $domain . '/upload/files/gallery/' . $request->user_id . '/' . $title . '.' . $extension;
                    // file_put_contents($fileImageLink, $image);

                    // $path = 'public/gallery/'. $request->user_id . '/' . $title . '.' . $extension;
                    // $storeResult = Storage::disk('local')->put($path, $image);
                    // $fileImageUrl = $protocol . '://' . $host . '/public/gallery/' . $request->user_id . '/' . $title . '.' . $extension;

                    $input['image'] = $fileImageUrl;
                    $input['user_id'] = Auth::user()->id;

                    $saveGallery = Gallery::create($input);

                    if ($saveGallery) {
                    } else {
                        return response()->json(['status' => 'failed', 'message' => 'upload failed'], 401);
                    }
                }

                return response()->json(['status' => 'success', 'message' => 'upload successfully', 'res' => $storagePath, 'fileImageUrl' => $fileImageUrl, 'url' => $url], $this->successStatus);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'user not found'], 200);
            }
        }


        // // IMAGE
        // if ($request->image !== '' && $request->image !== null) {
        //     $image_parts = explode(";base64,", $request->image);
        //     $image_type_aux = explode("image/", $image_parts[0]);
        //     $image_type = $image_type_aux[1];
        //     $image_base64 = base64_decode($image_parts[1]);
        //     $fileImageLink = $galleryPath . $request->user_id . '/' . $title . '.' . $image_type;
        //     $fileImageUrl = $protocol . '://' . $domain . '/upload/files/gallery/' . $request->user_id . '/' . $title . '.' . $image_type;
        //     file_put_contents($fileImageLink, $image_base64);
        // } else {
        //     $fileImageLink = '';
        //     $fileImageUrl = '';
        // }

        // $input = $request->all();

        // $input['image'] = $fileImageUrl;
        // $input['user_id'] = $request->user_id;

        // $saveGallery = Gallery::create($input);

        // if ($saveGallery) {
        //     return response()->json(['status' => 'success', 'message' => 'created successfully'], $this->successStatus);
        // } else {
        //     return response()->json(['status' => 'failed', 'message' => 'create failed'], 401);
        // }
    }

    public function deletePhotos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $arr = json_decode($request->id, true);

        for ($i = 0; $i < sizeof($arr); $i++) {
            $update = Gallery::where('id', $arr[$i]['id'])->update(
                [
                    'status_delete' => 1
                ]
            );

            if ($update) {
            } else {
                return response()->json(['status' => 'failed', 'message' => 'delete failed'], 401);
            }
        }
        return response()->json(['status' => 'success', 'message' => 'deleted successfully'], 200);
    }
}
