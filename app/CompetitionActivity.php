<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class CompetitionActivity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'competition_id',
        'winner_count'
    ];

    protected $casts = [
        'competition_id' => 'bigInteger'
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

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function classGrades()
    {
        return $this->belongsToMany(ClassGrade::class, "competition_activity_class_grades");
    }

    public function getAllClassesPerActivity($competition_activity_id)
    {
        $competitionActivity = CompetitionActivity::select(
            'competition_activities.id AS id',
            'name'
        )
            ->where('competition_activities.id', $competition_activity_id)
            ->first();

        $classCategories = ClassCategory::select(
            'id',
            'name'
        )
            ->where('competition_activity_id', $competition_activity_id)
            ->where('disabled', 0)
            ->whereNotIn('id', [19, 20, 21, 22, 23, 24, 25, 26, 33]) // custom id
            ->get();

        $classCountries = ClassCountry::select(
            'id',
            'name'
        )
            ->get();

        $classGrades = ClassGrade::select(
            'id',
            'name'
        )
            ->get();

        $collection = [];

        $columnNew = array();

        $obj = new stdClass();
        $obj->id = $competitionActivity->id;
        $obj->name = $competitionActivity->name;
        $obj->more = false;
        array_push($columnNew, $obj);

        $obj = new stdClass();
        $obj->id = null;
        $obj->name = 'COUNTRY';
        $obj->more = false;
        array_push($columnNew, $obj);

        foreach ($classCategories as $c) {
            $obj = new stdClass();
            $obj->id = $c['id'];
            $obj->name = $c['name'];
            $obj->more = true;
            array_push($columnNew, $obj);
        }

        $obj = new stdClass();
        $obj->id = $competitionActivity->id;
        $obj->name = 'buttons';
        $obj->more = false;
        array_push($columnNew, $obj);

        foreach ($classGrades as $object) {
            $classGradesArr[] = $object->toArray();
        }

        foreach ($classCountries as $object) {
            $classCountriesArr[] = $object->toArray();
        }

        // filter class grades
        if ($competition_activity_id == 3) { // Can Jam
            $classGradesArr = array_splice($classGradesArr, 1, 2);
        } else if ($competition_activity_id != 4) // not can craft
        {
            $classGradesArr = array_splice($classGradesArr, 0, 3);
        }

        $classGradesCopy = $classGradesArr;
        $classCountriesCopy = $classCountriesArr;

        $column = (object) array(
            $competitionActivity->name => array(),
            'COUNTRY' => array(),
        );

        foreach ($classCategories as $classCategory) {
            $key = $classCategory->name;
            $column->$key = array();
        }

        $classesArray = array();
        $classesLength = sizeof($classGradesArr) * sizeof($classCountriesArr);
        for ($i = 0; $i < $classesLength; $i++) {
            array_push($classesArray, new $column);
        }

        $classGradeCount = 0;
        for ($i = 0; $i < sizeof($classesArray); $i++) {

            $classes = $classesArray[$i];

            //class grades
            if (sizeof($classGradesCopy) > 0) {

                $_key = $competitionActivity->name;
                $classes->$_key = array($classGradesCopy[0]);
                $classGradesCopyTemp = $classGradesCopy[0];

                $classGradeCount++;

                if ($classGradeCount >= sizeof($classCountriesArr)) {
                    array_splice($classGradesCopy, 0, 1);
                    $classGradeCount = 0;
                }
            }

            if (sizeof($classGradesCopy) <= 0) {
                $classGradesCopy = $classGradesArr;
            }

            //class countries
            if (sizeof($classCountriesCopy) > 0) {

                $classes->COUNTRY = array($classCountriesCopy[0]);
                $classCountriesCopyTemp = $classCountriesCopy[0];

                array_splice($classCountriesCopy, 0, 1);
            }

            if (sizeof($classCountriesCopy) <= 0) {
                $classCountriesCopy = $classCountriesArr;
            }


            //class categories
            foreach ($classCategories as $classCategory) {
                $key = $classCategory->name;

                $classGroup = ClassGroup::select(
                    'id',
                    'name'
                )
                    ->where('class_grade_id', $classGradesCopyTemp['id'])
                    ->where('class_country_id', $classCountriesCopyTemp['id'])
                    ->where('class_category_id', $classCategory->id)
                    ->where('event_id', null)
                    ->where('disabled', 0)
                    ->get();


                $classes->$key = $classGroup;
            }
        }

        return ['column' => $columnNew, 'data' => $classesArray];
    }
}
