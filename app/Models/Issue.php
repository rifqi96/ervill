<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Inventory;
use App\Models\Order;

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

        //check if has issue type
        if(count($data) ==3 && $data['type']){
            $this->type = $data['type'];
        }else{
            $this->type = 'none';
        }
        
        $this->description = $data['description'];
        $this->quantity = $data['quantity'];
        
    }



    public function doDelete(){
        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(4);

        //recalculate inventory
        if($this->type=="Kesalahan Pabrik Air"){
            $broken_gallon->quantity -= $this->quantity;
            $filled_gallon->quantity += $this->quantity;
        }else if($this->type == "Refund Gallon"){
            $broken_gallon->quantity -= $this->quantity;
            $filled_gallon->quantity += $this->quantity;
        }
        else if($this->type == "Kesalahan Customer" ){
            $broken_gallon->quantity -= $this->quantity;
            $empty_gallon->quantity += $this->quantity;
        }


        //check if it is the last issue in the order
        if(count($this->order->issues) == 1){

            //check if it is orderWater
            if($this->order->orderWater){
                $this->order->orderWater->status = 'selesai';
                if( !$this->order->orderWater->save() ){
                    return false;
                }             
            }else if($this->order->orderCustomer){//check if it is orderCustomer
                if($this->order->orderCustomer->status == 'Bermasalah'){
                    $this->order->orderCustomer->status = 'Selesai';
                    if( !$this->order->orderCustomer->save() ){
                        return false;
                    }  
                }
            }            
        }

        if( !$empty_gallon->save() || !$filled_gallon->save() || !$broken_gallon->save() || !$outgoing_gallon->save() ){
            return false;
        }

        return $this->delete();
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
                        $query->whereDate('delivery_at', '=', Carbon::today()->toDateString());
                    }]);
                }]);
            }])
            ->whereHas('order', function($query){
//                $query->whereDate('delivery_at', '=', Carbon::today()->toDateString());
                $query->has('orderCustomer');
            })
            ->get();
    }
}
