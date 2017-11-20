<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\OrderCustomerIssue;

class OrderCustomer extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
    }

    public function doMake($order_data, $gallon_data, $customer_id)
    {
        $this->order_id = $order_data->id;
        $this->customer_id = $customer_id;
        $this->empty_gallon_quantity = 0;
        if($gallon_data->empty_gallon){
            $this->empty_gallon_quantity = $gallon_data->quantity;
        }
        $this->delivery_at = $gallon_data->delivery_at;
        $this->status = "Draft";
        return ($this->save());
    }

    public function doUpdate($orderGallon)
    {
        $this->outsourcing_driver_id = $orderGallon->outsourcing;

        return ($this->save());
    }

    public function doDelete(){

        return $this->delete();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }
}
