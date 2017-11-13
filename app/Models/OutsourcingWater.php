<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourcingWater extends Model
{
    public function orderWaters()
    {
        return $this->hasMany('App\Models\OrderWater');
    }
}
