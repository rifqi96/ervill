<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{

	use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $guarded = [];

	public function doUpdate($inventory)
    {
        $this->quantity = $inventory->quantity;
        $this->price = $inventory->price;
        
        return $this->save();
    }

    public function add($quantity)
    {    	
        $this->quantity += $quantity;
        
        return $this->save();
    }

    public function subtract($quantity)
    {    	
        $this->quantity -= $quantity;
        
        return $this->save();
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    public function issues()
    {
        return $this->hasMany('App\Models\Issue');
    }
}
