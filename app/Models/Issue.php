<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    /*
     * Types of Refund:
     * 1. Customer:
     *  a. "Refund Gallon" : Kesalahan driver, ganti gallonnya customer
     *  b. "Refund Cash" : Kesalahan driver, ganti uang customer
     *  c. "Kesalahan Customer" : Kesalahan customer, customer bayar ke ERVILL
     */

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    }
}
