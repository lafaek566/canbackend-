<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassGroup extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'class_grade_id',
        'class_country_id',
        'class_category_id',
        'association_id',
        'event_id',
        'disabled'
    ];

    protected $casts = [
        'class_grade_id' => 'bigInteger',
        'class_country_id' => 'bigInteger',
        'class_category_id' => 'bigInteger',
        'association_id' => 'bigInteger',
        'event_id' => 'bigInteger'
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

    public function eventMemberClass()
    {
        return $this->hasMany(EventMemberClass::class);
    }

    public function classGrade()
    {
        return $this->belongsTo(ClassGrade::class, 'class_grade_id');
    }
    public function classCountry()
    {
        return $this->belongsTo(ClassCountry::class, 'class_country_id');
    }
    public function classCategory()
    {
        return $this->belongsTo(ClassCategory::class, 'class_category_id');
    }
    public function eventId()
    {
        return $this->belongsTo(Event::class, 'id');
    }
}
