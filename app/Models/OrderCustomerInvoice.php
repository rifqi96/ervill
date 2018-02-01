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
}
