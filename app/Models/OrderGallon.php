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
