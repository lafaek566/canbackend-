<?php

namespace App\Http\Resources\Car;

use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public static function collection($resource)
    {
        return tap(new CarCollection($resource), function ($collection) {
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
            'avatar' => $this->avatar,
            'engine' => $this->engine,
            'power' => $this->power,
            'seat' => $this->seat,
            'transmission_type' => $this->transmission_type,
            'vehicle' => $this->vehicle,
            'license_plate' => $this->license_plate,
            'color' => $this->color,
            'team' => $this->team,
            'front_car_image' => $this->front_car_image,
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
