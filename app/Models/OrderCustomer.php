<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;

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

    public function doUpdate($data)
    {
        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);

        if($filled_gallon->quantity < $data->quantity){
            return false;
        }

        $empty_gallon->quantity = ($empty_gallon->quantity - $this->empty_gallon_quantity) + $data->empty_gallon_quantity;
        $filled_gallon->quantity = ($filled_gallon->quantity + $this->order->quantity) - $data->quantity;

        $this->order->quantity = $data->quantity;
        $this->empty_gallon_quantity = $data->empty_gallon_quantity;
        $this->delivery_at = $data->delivery_at;
        $this->status = $data->status;
        $this->customer_id = $data->customer_id;

        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save()){
            return false;
        }
        return ($this->save());
    }
}
