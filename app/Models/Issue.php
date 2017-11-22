<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Issue extends Model
{
    /*
     * Types of Refund:
     * 1. Customer:
     *  a. "Refund Gallon" : Kesalahan driver, ganti gallonnya customer
     *  b. "Refund Cash" : Kesalahan driver, ganti uang customer
     *  c. "Kesalahan Customer" : Kesalahan customer, customer bayar ke ERVILL
     */

    public function doMakeIssueOrderWater($issue,$data)
    {        
        $this->inventory_id = 2;
        $this->order_id = $issue->order_id;
        if(count($data) ==3 && $data['type']){
            $this->type = $data['type'];
        }else{
            $this->type = 'none';
        }
        
        $this->description = $data['description'];
        $this->quantity = $data['quantity'];
        
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    }

    public function getRecentIssues(){
        return $this->with(['order' => function($query){
                $query->with(['orderCustomer' => function($query){
                    $query->with(['customer', 'shipment' =>function($query){
                        $query->with('user');
                    }]);
                }]);
            }])
            ->whereDate('created_at', '=', Carbon::today()->toDateString())
            ->whereHas('order', function($query){
                $query->whereDate('created_at', '=', Carbon::today()->toDateString());
                $query->has('orderCustomer');
            })
            ->get();
    }
}
