<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCustomerReturnInvoice extends Model
{
    protected $guarded = [];

    public function orderCustomerReturn()
    {
        return $this->belongsTo('App\Models\OrderCustomerReturn');
    }
    public function reHeaderInvoice()
    {
        return $this->belongsTo('App\Models\ReHeaderInvoice');
    }
    public function price()
    {
        return $this->belongsTo('App\Models\Price');
    }

    public function doMake($order_customer_return, $nomor_struk, $type){


    	$this->re_header_invoice_id = $nomor_struk;
    	$this->order_customer_return_id = $order_customer_return->id;

      	if($type=="empty"){
	        if($order_customer_return->customer->type=="agent"){        	
	            $this->price_id = 13;
	            $this->quantity = $order_customer_return->empty_gallon_quantity;
	            $price = Price::find(13)->price;
	            $this->subtotal = ($this->quantity * $price);
	            $this->price_number = $price;
	        }else{
	            $this->price_id = 6;
	            $this->quantity = $order_customer_return->empty_gallon_quantity;
	            $price = Price::find(6)->price;
	            $this->subtotal = ($this->quantity * $price);
	            $this->price_number = $price;
	        }
	    }else{
	    	if($order_customer_return->customer->type=="agent"){        	
	            $this->price_id = 14;
	            $this->quantity = $order_customer_return->filled_gallon_quantity;
	            $price = Price::find(14)->price;
	            $this->subtotal = ($this->quantity * $price);
	            $this->price_number = $price;
	        }else{
	            $this->price_id = 7;
	            $this->quantity = $order_customer_return->filled_gallon_quantity;
	            $price = Price::find(7)->price;
	            $this->subtotal = ($this->quantity * $price);
	            $this->price_number = $price;
	        }
	    }
                   	


    	$this->save();
    	return $this;
    }
}
