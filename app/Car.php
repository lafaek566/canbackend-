<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
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
        'manual_input',
        'signal_flowchart',
        'power_supply_flowchart'
    ];

    /**
     * The dates attributes.
    *
    * @var array
    */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'user_id' => 'bigInteger',
        'engine' => 'integer',
        'power' => 'integer',
        'seat' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
