<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCustomerBuyInvoice extends Model
{
    protected $guarded = [];

    public function orderCustomerBuy()
    {
        return $this->belongsTo('App\Models\OrderCustomerBuy');
    }
    public function ocHeaderInvoice()
    {
        return $this->belongsTo('App\Models\OcHeaderInvoice');
    }
    public function price()
    {
        return $this->belongsTo('App\Models\Price');
    }

    public function doMake($order_customer_buy, $nomor_struk, $is_refill_and_add = false){


    	$this->oc_header_invoice_id = $nomor_struk;
    	$this->order_customer_buy_id = $order_customer_buy->id;

      
        if($order_customer_buy->customer->type=="agent"){
            $this->price_id = 12;
            $this->quantity = $order_customer_buy->quantity;
            $this->subtotal = ($this->quantity * Price::find(12)->price);
        }else{
            $this->price_id = 5;
            $this->quantity = $order_customer_buy->quantity;
            $this->subtotal = ($this->quantity * Price::find(5)->price);
        }
                   	


    	$this->save();
    	return $this;
    }

}
