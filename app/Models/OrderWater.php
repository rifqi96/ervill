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

    public function doUpdate($orderWater)
    {      
        $this->outsourcing_water_id = $orderWater->outsourcing_water;
        $this->outsourcing_driver_id = $orderWater->outsourcing_driver;
        if($this->order->accepted_at != null){
            $this->driver_name = $orderWater->driver_name;
        }
        $this->delivery_at = $orderWater->delivery_at;

        return ($this->save());
    }

    public function doConfirm($driver_name){
        $this->status = 'selesai';
        $this->driver_name = $driver_name;
        return ($this->save()); 
    }

    public function doCancel(){
        $this->status = 'proses';
        $this->driver_name = null;
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
}
