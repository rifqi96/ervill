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
    public function price()
    {
        return $this->belongsTo('App\Models\Price');
    }



    public function doMake($order_customer, $nomor_struk, $is_refill_and_add = false){


    	$this->oc_header_invoice_id = $nomor_struk;
    	$this->order_customer_id = $order_customer->id;

        if($is_refill_and_add){
            if($order_customer->customer->type=="agent"){
                $this->price_id = 8;
                $this->quantity = $order_customer->order->quantity;
                $this->subtotal = ($this->quantity * Price::find(8)->price);
                $this->price_number = Price::find(8)->price;
            }else{
                $this->price_id = 1;
                $this->quantity = $order_customer->order->quantity;
                $this->subtotal = ($this->quantity * Price::find(1)->price);
                $this->price_number = Price::find(1)->price;
            }
        }else{
            if($order_customer->purchase_type){
                if($order_customer->is_new=="false"){
                    $this->quantity = $order_customer->additional_quantity;
                }else{
                    $this->quantity = $order_customer->order->quantity;
                }

                if($order_customer->purchase_type=="rent"){
                    if($order_customer->customer->type=="agent"){
                        $this->price_id = 9;             
                        $this->subtotal = ($this->quantity * Price::find(9)->price);
                        $this->price_number = Price::find(9)->price;
                    }else{
                        $this->price_id = 2;  
                        $this->subtotal = ($this->quantity * Price::find(2)->price);
                        $this->price_number = Price::find(2)->price;
                    }
                    
                }else if($order_customer->purchase_type=="purchase"){
                    if($order_customer->customer->type=="agent"){
                        $this->price_id = 11;
                        $this->subtotal = ($this->quantity * Price::find(11)->price);
                        $this->price_number = Price::find(11)->price;
                    }else{
                        $this->price_id = 4;  
                        $this->subtotal = ($this->quantity * Price::find(4)->price);
                        $this->price_number = Price::find(4)->price;
                    }
                }else if($order_customer->purchase_type=="non_ervill"){
                    if($order_customer->customer->type=="agent"){
                        $this->price_id = 10;  
                        $this->subtotal = ($this->quantity * Price::find(10)->price);
                        $this->price_number = Price::find(10)->price;
                    }else{
                        $this->price_id = 3;  
                        $this->subtotal = ($this->quantity * Price::find(3)->price);
                        $this->price_number = Price::find(3)->price;
                    }               
                }
                
            }else{
                if($order_customer->customer->type=="agent"){
                    $this->price_id = 8;
                    $this->quantity = $order_customer->order->quantity;
                    $this->subtotal = ($this->quantity * Price::find(8)->price);
                    $this->price_number = Price::find(8)->price;
                }else{
                    $this->price_id = 1;
                    $this->quantity = $order_customer->order->quantity;
                    $this->subtotal = ($this->quantity * Price::find(1)->price);
                    $this->price_number = Price::find(1)->price;
                }
                
            }
        }

    	


    	$this->save();
    	return $this;
    }
}
