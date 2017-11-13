<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGallon extends Model
{
    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }
    public function outsourcingDriver()
    {
        return $this->belongsTo('App\Models\OutsourcingDriver');
    }
}
