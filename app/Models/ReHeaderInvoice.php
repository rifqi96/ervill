<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReHeaderInvoice extends Model
{
    protected $guarded = [];

    public function orderCustomerReturnInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerReturnInvoice');
    }
}
