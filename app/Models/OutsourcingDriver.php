<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourcingDriver extends Model
{
    public function orderGallons()
    {
        return $this->hasMany('App\Models\OrderGallon');
    }
    public function orderWaters()
    {
        return $this->hasMany('App\Models\OrderWater');
    }
}
