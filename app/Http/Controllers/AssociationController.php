<?php

namespace App\Http\Controllers;

use App\Association;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssociationController extends Controller
{
    public $successStatus = 200;

    /**
     * Get all association
     *
     * @return Response
     */
    public function listAll()
    {
        $association = Association::select(
            'id',
            'name',
            'logo',
        )
            ->orderBy('id')
            ->get();

        $arr = [];

        foreach ($association as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    /**
     * Get all association
     *
     * @return Response
     */
    public function listAllWithTrashed()
    {
        $association = Association::select(
            'id',
            'name',
            'logo',
            'deleted_at'
        )
            ->orderBy('id')
            ->withTrashed()
            ->get();

        $arr = [];

        foreach ($association as $object) {
            $arr[] = $object->toArray();
        }
        return response()->json($arr);
    }

    /**
     * Get detail association
     *
     * @return Response
     */
    public function detail(Association $association)
    {
        return response()->json($association->toArray());
    }

    /**
     * Store association
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:associations',
            'logo' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // BEGIN STORE

        $storagePath = public_path('upload/files/');
        $uploadPath = public_path('upload/');

        $host = \Config::get('project-config.project_host');
        $protocol = \Config::get('project-config.project_protocol');
        $domain = \Config::get('project-config.project_domain');

        $waktu = date('ymdHis');

        $logoNameArr = explode(' ', $request->name);
        $logoName = implode('-', $logoNameArr);

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath);
        }

        if (!file_exists($storagePath)) {
            mkdir($storagePath);
        }

        if (!file_exists($storagePath . 'logo-association')) {
            mkdir($storagePath . 'logo-association');
        }

        if ($request->logo !== '' && $request->logo !== null) {
            $image_parts = explode(";base64,", $request->logo);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileLogoLink = $storagePath . 'logo-association/' . $logoName . $waktu . '.' . $image_type;
            $fileLogoUrl = $host . '/public/upload/files/logo-association/' . $logoName . $waktu . '.' . $image_type;
            file_put_contents($fileLogoLink, $image_base64);
        } else {
            $fileLogoLink = '';
            $fileLogoUrl = '';
        }

        $input = $request->all();

        $input['logo'] = $fileLogoUrl;

        $saveEvent = Association::create($input);

        if ($saveEvent) {
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
    public function update(Request $request, Association $association)
    {
        $association = Association::select('id', 'name')
            ->where('name', $request->name)
            ->get();

        $associationOld = Association::select(
            'id',
            'logo',
            'name'
        )
            ->where('id', $request->id)
            ->get();

        $count = $association->count();

        if ($count > 0) {
            if ($associationOld[0]->id !== $association[0]->id) {
                return response()->json(['status' => 'failed', 'message' => 'name have been used'], 200);
            } else {
                return $this->updateAssociation($request, $associationOld);
            }
        } else {
            return $this->updateAssociation($request, $associationOld);
        }
    }

    protected function updateAssociation(Request $request, $associationOld)
    {
        $storagePath = public_path('upload/files/');
        $host = \Config::get('project-config.project_host');

        $waktu = date('ymdHis');

        $logoNameArr = explode(' ', $request->name);
        $logoName = implode('-', $logoNameArr);

        $cekImage = substr($request['logo'], 0, 10);

        if ($cekImage === 'data:image') {
            if ($associationOld[0]->logo !== '' && $associationOld[0]->logo !== null) {
                $path = parse_url($associationOld[0]->logo, PHP_URL_PATH);
                $file_name = basename($path);
                $fileLogoLink = $storagePath . 'logo-association/' . $file_name;

                if (file_exists($fileLogoLink)) {
                    unlink($fileLogoLink);
                }
            }
            $image_parts = explode(";base64,", $request->logo);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $fileLogoLink = $storagePath . 'logo-association/' . $logoName . $waktu . '.' . $image_type;
            $fileLogoUrl = $host . '/upload/files/logo-association/' . $logoName . $waktu . '.' . $image_type;
            file_put_contents($fileLogoLink, $image_base64);
        } else {
            $fileLogoUrl = $request['logo'];
        }

        $associationUpdate['logo'] = $fileLogoUrl;
        $associationUpdate['name'] = $request->name;
        $update = Association::where('id', $request->id)->update(
            [
                'logo' => $associationUpdate['logo'],
                'name' => $associationUpdate['name'],
            ]
        );

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'updated successfully'], 200);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'update failed'], 401);
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Association $association
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, Association $association)
    {
        $delete = $association->delete();

        if ($delete) {
            $success = ['status' => 'success', 'message' => 'deleted successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'delete failed'];
        }

        return response()->json($success, $this->successStatus);
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Association $association
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request)
    {
        $restore = Association::where("id", $request->id)->withTrashed()->restore();

        if ($restore) {
            $success = ['status' => 'success', 'message' => 'restored successfully'];
        } else {
            $success = ['status' => 'failed', 'message' => 'restore failed'];
        }

        return response()->json($success, $this->successStatus);
    }
}
