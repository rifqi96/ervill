<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCustomerInvoice extends Model
{
    protected $guarded = [];

    public function orderCustomer()
    {
        return $this->belongsTo('App\Models\OrderCustomer');
    }

    public function ocHeaderInvoice()
    {
        return $this->belongsTo('App\Models\OcHeaderInvoice');
    }

    public function doMake($order_customer, $nomor_struk){


    	$this->oc_header_invoice_id = $nomor_struk;
    	$this->order_customer_id = $order_customer->id;

    	if($order_customer->purchase_type){
    		if($order_customer->purchase_type=="rent"){
    			$this->price_id = xxx;	
    			$this->subtotal = xxx;
    		}else if($order_customer->purchase_type=="purchase"){
    			$this->price_id = xxx;
    			$this->subtotal = xxx;
    		}else if($order_customer->purchase_type=="non_ervill"){
    			$this->price_id = xxx;
    			$this->subtotal = xxx;    			
    		}
    		if($order_customer->is_new=="false"){
				$this->quantity = $order_customer->additional_quantity;
			}else{
				$this->quantity = $order_customer->order->quantity;
			}
    	}else{
    		$this->price_id = xxx;
    		$this->quantity = $order_customer->order->quantity;
    		$this->subtotal = xxx;
    	}


    	$this->save();
    	return $this;
    }
}
