<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderWater extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];
    
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function outsourcingWater()
    {
        return $this->belongsTo('App\Models\OutsourcingWater');
    }
    public function outsourcingDriver()
    {
        return $this->belongsTo('App\Models\OutsourcingDriver');
    }
}
