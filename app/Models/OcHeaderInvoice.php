<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcHeaderInvoice extends Model
{
    protected $guarded = [];

    public function orderCustomerInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerInvoice');
    }
    public function orderCustomerBuyInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerBuyInvoice');
    }
}
