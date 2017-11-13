<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCustomer extends Model
{
    public function order()
    {
        return $this->hasOne('App\Models\Order');
    }
    public function issues()
    {
        return $this->belongsToMany('App\Models\Issue');
    }
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
    }
}
