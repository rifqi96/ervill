<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourcingWater extends Model
{

	public function doUpdate($outsourcingWater)
    {   
        $this->name = $outsourcingWater->name;
        return ($this->save());
    }

    public function doMake($outsourcingWater)
    {
        $this->name = $outsourcingWater->name;
        return ($this->save());
    }

    public function orderWaters()
    {
        return $this->hasMany('App\Models\OrderWater');
    }
}
