<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourcingDriver extends Model
{

	public function doUpdate($outsourcingDriver)
    {   
        $this->name = $outsourcingDriver->name;
        
        return ($this->save());
    }

    public function doMake($outsourcingDriver)
    {
        $this->name = $outsourcingDriver->name;
        return ($this->save());
    }

    public function orderGallons()
    {
        return $this->hasMany('App\Models\OrderGallon');
    }
    public function orderWaters()
    {
        return $this->hasMany('App\Models\OrderWater');
    }
}
