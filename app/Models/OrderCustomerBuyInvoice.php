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
}
