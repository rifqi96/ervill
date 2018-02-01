<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGallon extends Model
{
    protected $guarded = [];


    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }

    public function doMake($data,$customer_id)
    {
        $this->customer_id = $customer_id;
        $this->qty = $data->quantity;
        $this->type = $data->purchase_type;     
        $this->save();   
        return $this;
    }

    public function doMakeAdd($data,$customer_id)
    {
        $this->customer_id = $customer_id;
        $this->qty = $data->add_gallon_quantity;
        $this->type = $data->add_gallon_purchase_type;        
        return ($this->save());
    }
}
