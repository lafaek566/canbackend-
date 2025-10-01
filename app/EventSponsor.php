<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventSponsor extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'event_id',
        'sponsor_id',
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'event_id' => 'bigInteger',
        'sponsor_id' => 'bigInteger',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class,'event_id');
    }
    public function sponsor()
    {
        return $this->belongsTo(User::class,'sponsor_id');
    }

    public function getAllSponsorsByEventId($event_id) {
        $eventSponsor = EventSponsor::select(
            'sponsor_id',
            'users.name AS sponsor_name',
            'users.sponsor_type AS sponsor_type',
            'user_profiles.avatar AS sponsor_avatar',
            'countries.id AS country_id',
            'countries.name AS country_name',
            'associations.id AS associations_id',
            'associations.name AS associations_name'
        )
            ->join('users', 'users.id', '=', 'event_sponsors.sponsor_id')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'event_sponsors.sponsor_id')
            ->leftJoin('country_sponsors', 'country_sponsors.user_id', '=', 'event_sponsors.sponsor_id')
            ->leftJoin('countries', 'countries.id', '=', 'country_sponsors.country_id')
            ->leftJoin('association_sponsors', 'association_sponsors.user_id', '=', 'event_sponsors.sponsor_id')
            ->leftJoin('associations', 'associations.id', '=', 'association_sponsors.association_id')
            ->where('event_id', '=', $event_id)
            ->get();

        $arr = [];

        foreach ($eventSponsor as $object) {
            $arr[] = $object->toArray();
        }

        return $arr;
    }
}
