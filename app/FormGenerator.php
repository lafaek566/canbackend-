<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormGenerator extends Model
{
     /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'title',
        'form_assessment',
        'audio_player_ids',
        "status_public",
        'user_id',
        'status_delete',
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
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
