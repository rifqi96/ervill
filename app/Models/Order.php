<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    }
    public function orderGallon()
    {
        return $this->belongsTo('App\Models\OrderGallon');
    }
    public function orderWater()
    {
        return $this->belongsTo('App\Models\OrderWater');
    }
    public function orderCustomer()
    {
        return $this->belongsTo('App\Models\OrderCustomer');
    }
}
