<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutsourcingDriver extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

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

    public function doRestore(){
        return $this->restore();
    }

    public function doDelete(){
        return $this->delete();
    }

    public function doForceDelete(){
        return $this->forceDelete();
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
