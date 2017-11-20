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

    public function doMake($order, $orderWater)
    {        
        $this->outsourcing_water_id = $orderWater->outsourcing_water;
        $this->outsourcing_driver_id = $orderWater->outsourcing_driver;
        $this->order_id = $order->id;
        $this->delivery_at = $orderWater->delivery_at;
        $this->status = 'proses';
        return ($this->save());
    }
    
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
    public function issues()
    {
        return $this->belongsToMany('App\Models\Issue');
    }
}
