<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    //

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'user_id',
        'audio',
        'local_path',
        'artist',
        'title',
        'file_name',
        'mime_type',
        'duration',
        'size',
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
