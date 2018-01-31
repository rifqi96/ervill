<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderWater extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    public function doMake($data, $author_id)
    {
        $order = (new Order)->doMakeOrderWater($data, $author_id);

//        $this->outsourcing_water_id = $data->outsourcing_water;
        $this->outsourcing_driver_id = $data->outsourcing_driver;
        $this->order_id = $order->id;
        $this->buffer_qty = $data->buffer_qty;
        $this->warehouse_qty = $data->warehouse_qty;
        $this->delivery_at = $data->delivery_at;
        $this->status = 'proses';
        return ($this->save());
    }

    public function doUpdate($data)
    {

        $empty_buffer_gallon = Inventory::find(1);
        $empty_warehouse_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $old_data = $this->toArray();

        //recalculate inventory if order is finished
        if($this->order->accepted_at != null){
            $empty_buffer_gallon->quantity -= ($data->buffer_qty - $this->buffer_qty);
            $empty_warehouse_gallon->quantity -= ($data->warehouse_qty - $this->warehouse_qty);
            $filled_gallon->quantity += (($data->buffer_qty + $data->warehouse_qty) - $this->order->quantity);

            //set driver name
            $this->driver_name = $data->driver_name;
        }

        //update order water and order data
//        $this->outsourcing_water_id = $data->outsourcing_water;
        $this->outsourcing_driver_id = $data->outsourcing_driver;
        $this->delivery_at = $data->delivery_at;
        $this->order->quantity = $data->buffer_qty + $data->warehouse_qty;
        $this->buffer_qty = $data->buffer_qty;
        $this->warehouse_qty = $data->warehouse_qty;

        if($empty_buffer_gallon->quantity<0){
            $empty_buffer_gallon->quantity = 0;
        }
        else if($empty_warehouse_gallon->quantity<0){
            $empty_warehouse_gallon->quantity = 0;
        }

        if(!$this->order->save() || !$empty_buffer_gallon->save() || !$empty_warehouse_gallon->save() || !$filled_gallon->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }

        return ($this->save());
    }

    public function doAddToEditHistory($old_data, $data){

        //set old values
//        $old_data['outsourcing_water_name'] = $old_data['outsourcing_water']['name'];
        $old_data['outsourcing_driver_name'] = $old_data['outsourcing_driver']['name'];
//        $old_data['quantity'] = $old_data['order']['quantity'];
        $old_data['delivery_at'] = Carbon::parse($old_data['delivery_at'])->format('Y-n-d');
     
        unset($old_data['id']); 
//        unset($old_data['outsourcing_water_id']);
        unset($old_data['outsourcing_driver_id']);
        unset($old_data['order_id']);  
        unset($old_data['status']);
//        unset($old_data['outsourcing_water']);
        unset($old_data['outsourcing_driver']);
        unset($old_data['order']);

        $old_value = '';
//        $old_value .= $old_data['outsourcing_water_name'] . ';';
        $old_value .= $old_data['outsourcing_driver_name'] . ';';
        $old_value .= $old_data['driver_name'] . ';';
        $old_value .= $old_data['buffer_qty'] . ';';
        $old_value .= $old_data['warehouse_qty'] . ';';
        $old_value .= $old_data['delivery_at'];


        //set new values
        $new_value_obj = $data->toArray(); 
//        $new_value_obj['outsourcing_water'] = OutsourcingWater::find($new_value_obj['outsourcing_water'])->name;
        $new_value_obj['outsourcing_driver'] = OutsourcingDriver::find($new_value_obj['outsourcing_driver'])->name;
        
        unset($new_value_obj['id']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        unset($new_value_obj['max_quantity']);

        $new_value = '';
//        $new_value .= $new_value_obj['outsourcing_water'] . ';';
        $new_value .= $new_value_obj['outsourcing_driver'] . ';';
        $new_value .= $new_value_obj['driver_name'] . ';';
        $new_value .= $new_value_obj['buffer_qty'] . ';';
        $new_value .= $new_value_obj['warehouse_qty'] . ';';
        $new_value .= $new_value_obj['delivery_at'];

        $edit_data = array(
            'module_name' => 'Order Water',
            'data_id' => $data->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => auth()->id()
        );

        return EditHistory::create($edit_data);
    }

    public function doConfirm($driver_name){

        $empty_buffer_gallon = Inventory::find(1);
        $empty_warehouse_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);

        //recalculate inventory
        $empty_buffer_gallon->quantity -= ($this->buffer_qty);
        $empty_warehouse_gallon->quantity -= ($this->warehouse_qty);
        $filled_gallon->quantity += ($this->order->quantity);

        //update order water and order data
        $this->status = 'selesai';
        $this->driver_name = $driver_name;
        $this->order->accepted_at = Carbon::now();

        // if($empty_gallon->quantity<0){
        //     $empty_gallon->quantity = 0;
        // }

        if(!$this->order->save() || !$empty_buffer_gallon->save() || !$empty_warehouse_gallon->save() || !$filled_gallon->save() ){
            return false;
        }

        return ($this->save()); 
    }

    public function doCancel(){

        $empty_buffer_gallon = Inventory::find(1);
        $empty_warehouse_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);

        //for handling the weird ajax error
        if($this->status =='proses'){
            return false;
        }

        //recalculate inventory from issues
        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Kesalahan Pabrik Air" || $issue->type=="Kesalahan Pengemudi"){
                    $broken_gallon->quantity -= $issue->quantity;
                    $filled_gallon->quantity += $issue->quantity;
                }

                //delete issue
                $issue->delete();
            }
        }

        //recalculate inventory
        $empty_buffer_gallon->quantity += ($this->buffer_qty);
        $empty_warehouse_gallon->quantity += ($this->warehouse_qty);
        $filled_gallon->quantity -= ($this->order->quantity);

        //update order water and order data
        $this->status = 'proses';
        $this->driver_name = null;
        $this->order->accepted_at = null;

        // if($filled_gallon->quantity<0){
        //     $filled_gallon->quantity = 0;
        // }

        if(!$this->order->save() || !$empty_buffer_gallon->save() || !$empty_warehouse_gallon->save() || !$filled_gallon->save() || !$broken_gallon->save() ){
            return false;
        }

        return ($this->save()); 
    }

    public function doConfirmWithIssue($data,$issueGallonDriver,$issueGallon,$issueSeal,$issueTissue){

        $empty_buffer_gallon = Inventory::find(1);
        $empty_warehouse_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);

        //set quantity for recalculating inventory and save issues
//        $empty_gallon_quantity_change = $this->order->quantity;
        $filled_gallon_quantity_change = $this->order->quantity;
        $broken_gallon_quantity_change = 0;

        if($data->typeGallonDriver){
            $issueGallonDriver->save();                    
            $filled_gallon_quantity_change -= $issueGallonDriver->quantity;
            $broken_gallon_quantity_change += $issueGallonDriver->quantity;
        }
        if($data->typeGallon){
            $issueGallon->save();                    
            $filled_gallon_quantity_change -= $issueGallon->quantity;
            $broken_gallon_quantity_change += $issueGallon->quantity;
        }
        if($data->typeSeal){
            $issueSeal->save();
        }
        if($data->typeTissue){
            $issueTissue->save();    
        }

        //recalculate inventory
        $empty_buffer_gallon->quantity -= ($this->buffer_qty);
        $empty_warehouse_gallon->quantity -= ($this->warehouse_qty);
        $filled_gallon->quantity += $filled_gallon_quantity_change;
        $broken_gallon->quantity += $broken_gallon_quantity_change;

        //update order water and order data
        $this->status = 'bermasalah';
        $this->driver_name = $data->driver_name;
        $this->order->accepted_at = Carbon::now();

        // if($empty_gallon->quantity<0){
        //     $empty_gallon->quantity = 0;
        // }


        if(!$this->order->save() || !$empty_buffer_gallon->save() || !$empty_warehouse_gallon->save() || !$filled_gallon->save() || !$broken_gallon->save()){
            return false;
        }

        return ($this->save()); 
    }

    public function doDelete($description, $author_id){

        //check if the order is on process
        if($this->status=='proses'){
            if(!$this->doAddToDeleteHistory($description, $author_id)){
                return false;
            }
            return $this->order->doDelete();
        }

        $empty_buffer_gallon = Inventory::find(1);
        $empty_warehouse_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);

        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Kesalahan Pabrik Air" || $issue->type=="Kesalahan Pengemudi"){
                    $broken_gallon->quantity -= $issue->quantity;
                    $filled_gallon->quantity += $issue->quantity;
                }
            }
        }

        $filled_gallon->quantity -= $this->order->quantity;
        $empty_buffer_gallon->quantity += $this->buffer_qty;
        $empty_warehouse_gallon->quantity += $this->warehouse_qty;

        // if($empty_gallon->quantity<0){
        //     $empty_gallon->quantity = 0;
        // }
        // else if($broken_gallon->quantity<0){
        //     $broken_gallon->quantity = 0;
        // }
        // else if($filled_gallon->quantity<0){
        //     $filled_gallon->quantity = 0;
        // }
        // if($broken_gallon->quantity<0){
        //     $broken_gallon->quantity = 0;
        // }

        if(!$filled_gallon->save() || !$broken_gallon->save() || !$empty_buffer_gallon->save() || !$empty_warehouse_gallon->save() || !$this->doAddToDeleteHistory($description, $author_id)){
            return false;
        }
        return $this->order->doDelete();
    }

    public function doAddToDeleteHistory($description, $author_id){
        $data = array(
            'module_name' => 'Order Water',
            'description' => $description,
            'data_id' => $this->id,
            'user_id' => $author_id
        );

        return DeleteHistory::create($data);
    }

    public function doRestore(){
        $empty_buffer_gallon = Inventory::find(1);
        $empty_warehouse_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);

        if($this->status!='proses'){
            if($this->order->issues){
                foreach($this->order->issues as $issue){
                    if($issue->type == "Kesalahan Pabrik Air" || $issue->type=="Kesalahan Pengemudi"){
                        $broken_gallon->quantity += $issue->quantity;
                        $filled_gallon->quantity -= $issue->quantity;
                    }
                }
            }

            $filled_gallon->quantity += $this->order->quantity;
            $empty_buffer_gallon->quantity -= $this->buffer_qty;
            $empty_warehouse_gallon->quantity -= $this->warehouse_qty;

            // if($empty_gallon->quantity<0){
            //     $empty_gallon->quantity = 0;
            // }
            // else if($broken_gallon->quantity<0){
            //     $broken_gallon->quantity = 0;
            // }
            // else if($filled_gallon->quantity<0){
            //     $filled_gallon->quantity = 0;
            // }

            if(!$filled_gallon->save() || !$empty_buffer_gallon->save() || !$empty_warehouse_gallon->save() || !$broken_gallon->save()){
                return false;
            }
        }

        return $this->order->doRestore();
    }

    public function doForceDelete(){
        if(!$this->delete()){
            return false;
        }

        return $this->order->forceDelete();
    }
    
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function outsourcingWater()
    {
        return $this->belongsTo('App\Models\OutsourcingWater');
    }
    public function outsourcingDriver()
    {
        return $this->belongsTo('App\Models\OutsourcingDriver');
    }
}