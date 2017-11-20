<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGallon extends Model
{
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

	public function doMake($order, $orderGallon)
    {        
        $this->outsourcing_driver_id = $orderGallon->outsourcing_driver;
        $this->order_id = $order->id;
        return ($this->save());
    }

    public function doUpdate($orderGallon)
    {              
        $this->outsourcing_driver_id = $orderGallon->outsourcing;   
        if($this->order->accepted_at != null){
            $this->driver_name = $orderGallon->driver_name;
        }     
        return ($this->save());
    }

    public function doConfirm($driver_name){
        $this->driver_name = $driver_name;
        return ($this->save()); 
    }

    public function doCancel(){
        $this->driver_name = null;
        return ($this->save()); 
    }
    

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function outsourcingDriver()
    {
        return $this->belongsTo('App\Models\OutsourcingDriver');
    }
}
