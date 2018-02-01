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
}
