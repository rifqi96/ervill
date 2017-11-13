<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    public function orderWaters()
    {
        return $this->belongsToMany('App\Models\OrderWater');
    }
    public function orderCustomers()
    {
        return $this->belongsToMany('App\Models\OrderCustomer');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    }
}
