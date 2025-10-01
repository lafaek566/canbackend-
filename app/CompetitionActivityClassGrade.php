<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompetitionActivityClassGrade extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'competition_activity_id',
        'class_grade_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'competition_activity_id' => 'bigInteger',
        'class_grade_id' => 'bigInteger'
    ];

    public function competitionActivity()
    {
        return $this->belongsTo(CompetitionActivity::class, 'competition_activity_id');
    }
    public function classGrade()
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }
}
