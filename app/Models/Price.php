<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $guarded = [];

    public function ocInvoices(){
        return $this->hasMany('App\Models\OrderCustomerInvoice');
    }

    public function ocBuyInvoices(){
        return $this->hasMany('App\Models\OrderCustomerBuyInvoice');
    }

    public function ocReturnInvoices(){
        return $this->hasMany('App\Models\OrderCustomerReturnInvoice');
    }
}
