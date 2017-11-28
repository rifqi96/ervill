<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Inventory;

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

    public function doMakeIssueOrderCustomer($order, $data)
    {        
        //check if input field is empty or not
        if(!$data->type || !$data->description || !$data->quantity){
            return false;
        }

        //check if quantity is integer or not
        if( !filter_var($data->quantity, FILTER_VALIDATE_INT) ){
            return false;
        }

        // //check if issue exceeds max quantity in an order
        // foreach($order->issues as $issue){
        // }
        // if(filter_var($data->quantity, FILTER_VALIDATE_INT) > $order->quantity){
        //     return false;
        // }

        $this->inventory_id = 2;
        $this->order_id = $order->id;
        $this->type = $data->type;    
        $this->description = $data->description;
        $this->quantity = $data->quantity;

        //recalculate inventory
        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);

        if($data->type == "Refund Gallon"){
            $broken_gallon->quantity += $data->quantity;
            $filled_gallon->quantity -= $data->quantity;
        }
        else{
            $broken_gallon->quantity += $data->quantity;
            $empty_gallon->quantity -= $data->quantity;
        }

        if( !$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() ){
            return false;
        }

        return $this->save();
        
    }

    public function doDelete(){
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);

        //recalculate inventory
        if($this->type=="Kesalahan Pabrik Air"){
            $broken_gallon->quantity -= $this->quantity;
            $filled_gallon->quantity += $this->quantity;
        }

        //check if it is the last issue in the order
        if(count($this->order->issues) == 1){

            //check if it is orderWater
            if($this->order->orderWater){
                $this->order->orderWater->status = 'selesai';
                if( !$this->order->orderWater->save() ){
                    return false;
                }             
            }            
        }

        if( !$filled_gallon->save() || !$broken_gallon->save() ){
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
