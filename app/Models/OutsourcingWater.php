<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutsourcingWater extends Model
{

    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


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

    public function doDelete(){
        return $this->delete();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }

    public function doRestore(){
        return $this->restore();
    }

    public function orderWaters()
    {
        return $this->hasMany('App\Models\OrderWater');
    }
}
