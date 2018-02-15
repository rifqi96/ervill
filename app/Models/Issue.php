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
        $this->inventory_id = 3;
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

        //count kesalahan customer quantity
        $kc_quantity = 0;
        foreach ($order->issues as $issue) {
            if($issue->type=="Kesalahan Customer"){
                $kc_quantity += $issue->quantity;
            }
        }

        $available_empty_gallon_quantity = $order->orderCustomer->empty_gallon_quantity - $kc_quantity;

        if($data->type=="Kesalahan Customer" && $data->quantity > $available_empty_gallon_quantity){
            return false;
        }

        // //check if issue exceeds max quantity in an order
        // foreach($order->issues as $issue){
        // }
        // if(filter_var($data->quantity, FILTER_VALIDATE_INT) > $order->quantity){
        //     return false;
        // }

        $this->inventory_id = 3;
        $this->order_id = $order->id;
        $this->type = $data->type;    
        $this->description = $data->description;
        $this->quantity = $data->quantity;

        //recalculate inventory
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);

        if($data->type == "Refund Gallon"){
            $broken_gallon->quantity += $data->quantity;
            $filled_gallon->quantity -= $data->quantity;
        }
        else if($data->type == "Kesalahan Customer" ){
            $broken_gallon->quantity += $data->quantity;
            $empty_gallon->quantity -= $data->quantity;
        }

        //if the order is finished, change to bermasalah     
        if($order->orderCustomer->status == 'Selesai'){
            $order->orderCustomer->status = 'Bermasalah';
        }

        if( !$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() || !$order->orderCustomer->save() ){
            return false;
        }

        return $this->save();
        
    }


    public function doCancelTransaction($order)
    {        

        //delete all existing issues in this order
        foreach($order->issues as $issue){
            if( !$issue->doDelete() ){
                return false;
            }
        }
     
        $this->inventory_id = 3;
        $this->order_id = $order->id;
        $this->type = 'Cancel Transaction';    
        $this->description = 'Transaksi Dibatalkan. Silahkan hubungi driver yang bersangkutan';
        $this->quantity = $order->quantity+$order->orderCustomer->additional_quantity;

        //recalculate inventory
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);
        $non_ervill_gallon = Inventory::find(6);

        $empty_gallon->quantity -= $order->orderCustomer->empty_gallon_quantity;
        $filled_gallon->quantity += ($order->quantity+$order->orderCustomer->additional_quantity);
        if($order->orderCustomer->purchase_type=="rent"){
            $outgoing_gallon->quantity -= ($order->quantity + $order->orderCustomer->additional_quantity - $order->orderCustomer->empty_gallon_quantity);
        }else if($order->orderCustomer->purchase_type=="non_ervill"){
            $non_ervill_gallon->quantity -= ($order->quantity + $order->orderCustomer->additional_quantity - $order->orderCustomer->empty_gallon_quantity);
        }
        
    
        //change status to bermasalah
        $order->orderCustomer->status = 'Bermasalah';
        

        if( !$filled_gallon->save() || !$empty_gallon->save() || !$outgoing_gallon->save() || !$non_ervill_gallon->save() || !$order->orderCustomer->save() ){
            return false;
        }

        return $this->save();
        
    }

    public function doDelete(){
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);
        $outgoing_gallon = Inventory::find(5);
        $non_ervill_gallon = Inventory::find(6);

        //recalculate inventory
        if($this->type=="Kesalahan Pabrik Air" || $this->type=="Kesalahan Pengemudi"){
            $broken_gallon->quantity -= $this->quantity;
            $filled_gallon->quantity += $this->quantity;
        }else if($this->type == "Refund Gallon"){
            $broken_gallon->quantity -= $this->quantity;
            $filled_gallon->quantity += $this->quantity;
        }
        else if($this->type == "Kesalahan Customer" ){
            $broken_gallon->quantity -= $this->quantity;
            $empty_gallon->quantity += $this->quantity;
        }else if($this->type == "Cancel Transaction"){
            $empty_gallon->quantity += $this->order->orderCustomer->empty_gallon_quantity;
            $filled_gallon->quantity -= $this->quantity;
            if($this->order->orderCustomer->purchase_type=="rent"){
                $outgoing_gallon->quantity += ($this->quantity - $this->order->orderCustomer->empty_gallon_quantity);
            }else if($this->order->orderCustomer->purchase_type=="non_ervill"){
                $non_ervill_gallon->quantity += ($this->quantity - $this->order->orderCustomer->empty_gallon_quantity);
            }
            

            //change status to proses
            $this->order->orderCustomer->status = 'Proses';

            if( !$this->order->orderCustomer->save() ){
                return false;
            }

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

        if( !$empty_gallon->save() || !$filled_gallon->save() || !$broken_gallon->save() || !$outgoing_gallon->save() || !$non_ervill_gallon->save() ){
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
//        return $this->with(['order' => function($query){
//                $query->with(['orderCustomer' => function($query){
//                    $query->with(['customer', 'shipment' =>function($query){
//                        $query->with('user');
//                        $query->whereDate('delivery_at', '=', Carbon::today()->toDateString());
//                    }]);
//                }]);
//            }])
//            ->whereHas('order', function($query){
//                // $query->has('orderCustomer');
//                $query->whereHas('orderCustomer', function($query){
//                    $query->whereDate('delivery_at', '=', Carbon::today()->toDateString());
//                });
//            })
//            ->get();
    }
}
