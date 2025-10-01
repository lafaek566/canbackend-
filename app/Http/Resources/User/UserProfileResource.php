<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;

class UserProfileResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'biography' => $this->biography,
            'phone_no' => $this->phone_no,
            'avatar' => $this->avatar,
            'sponsor_type' => $this->sponsor_type,
        ];
    }
}