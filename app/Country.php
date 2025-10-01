<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'selected' // Perubahan di sini: 'country_code' jadi 'code'
    ];

    /**
     * The dates attributes.
     *
     * @var array
     */
    // Anda bisa hapus protected $dates jika Anda menggunakan Laravel 5.5+
    // Namun, jika dibiarkan, ini sudah benar.
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}