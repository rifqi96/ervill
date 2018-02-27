<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderCustomerNonErvill extends Model
{
    protected $guarded = [];

    public function priceMaster(){
        return $this->belongsTo('App\Models\Price', 'price_id');
    }

    public function neHeaderInvoice(){
        return $this->belongsTo('App\Models\NeHeaderInvoice');
    }


    public function doMake($data, $invoice_no, $additional_price = null)
    {
        $price = Price::find($data->price_id);
        $this->ne_header_invoice_id = $invoice_no;
        $this->name = $price->name;
        if($additional_price){
            $this->price = $price->price + $additional_price;
        }
        else{
            $this->price = $price->price;
        }
        $this->price_id = $data->price_id;
        $this->quantity = $data->qty;
        $this->subtotal = $this->quantity * $this->price;

        return $this->save();
    }
}
