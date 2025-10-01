<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'competition_activity_id',
        'disabled'
    ];

    protected $casts = [
        'competition_activity_id' => 'bigInteger'
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

    public function competitionActivity()
    {
        return $this->belongsTo(CompetitionActivity::class, 'competition_activity_id');
    }
}
