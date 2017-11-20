<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    }
}
