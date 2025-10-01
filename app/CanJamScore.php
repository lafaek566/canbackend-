<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanJamScore extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'can_jam_score_history_id'
    ];

    protected $casts = [
        'can_jam_score_history_id' => 'bigInteger'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function canJamScoreHistory()
    {
        return $this->belongsTo(CanJamScoreHistory::class, 'can_jam_score_history_id');
    }
}
