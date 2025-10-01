<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;

use App\Http\Resources\Car\CarResource;
class UserResource extends Resource
{
    public static function collection($resource)
    {
        return tap(new UserCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }

    /**
     * @var array
     */
    protected $withoutFields = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->filterFields([
            'id' => $this->id,
            'name' => $this->name,
            'role_id' => (int)$this->role_id,
            'email' => $this->email,
            'user_profiles' => UserProfileResource::make($this->whenLoaded('userProfile')),
            'car' => CarResource::collection($this->whenLoaded('userCars')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,            
        ]);
    }

    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return $this
     */
    public function hide(array $fields)
    {
        $this->withoutFields = $fields;
        return $this;
    }

    /**
     * Remove the filtered keys.
     *
     * @param $array
     * @return array
     */
    protected function filterFields($array)
    {
        return collect($array)->forget($this->withoutFields)->toArray();
    }
  
}
