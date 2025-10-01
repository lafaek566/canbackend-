<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CanQSpectralBalanceAndLinearity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_member_class_id',
        'spectral_balance',
        'linearity',
        'spectral_balance_and_linearity_total'
    ];

    protected $casts = [
        'event_member_class_id' => 'bigInteger',
        'spectral_balance' => 'float',
        'linearity' => 'float',
        'spectral_balance_and_linearity_total' => 'float'
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

    public function eventMemberClass()
    {
        return $this->belongsTo(EventMemberClass::class, 'event_member_class_id');
    }
}
