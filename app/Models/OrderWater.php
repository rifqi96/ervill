<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderWater extends Model
{
    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }
    public function outsourcingWater()
    {
        return $this->belongsTo('App\Models\OutsourcingWater');
    }
    public function outsourcingDriver()
    {
        return $this->belongsTo('App\Models\OutsourcingDriver');
    }
    public function issues()
    {
        return $this->belongsToMany('App\Models\Issue');
    }
}
