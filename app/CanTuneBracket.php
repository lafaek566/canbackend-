<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanTuneBracket extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'class_grade_id'
    ];

    protected $casts = [
        'class_grade_id' => 'bigInteger'
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

    public function classGrade()
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }
}
