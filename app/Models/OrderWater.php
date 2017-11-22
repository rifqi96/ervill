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

    public function doMake($order, $orderWater)
    {        
        $this->outsourcing_water_id = $orderWater->outsourcing_water;
        $this->outsourcing_driver_id = $orderWater->outsourcing_driver;
        $this->order_id = $order->id;
        $this->delivery_at = $orderWater->delivery_at;
        $this->status = 'proses';
        return ($this->save());
    }

    public function doUpdate($data)
    {      

        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $old_data = $this->toArray();

        //recalculate inventory if order is finished
        if($this->order->accepted_at != null){
            $empty_gallon->quantity -= ($data->quantity - $this->order->quantity);
            $filled_gallon->quantity += ($data->quantity - $this->order->quantity);

            //set driver name
            $this->driver_name = $data->driver_name;
        }

        //update order water and order data
        $this->outsourcing_water_id = $data->outsourcing_water;
        $this->outsourcing_driver_id = $data->outsourcing_driver;
        $this->delivery_at = $data->delivery_at;
        $this->order->quantity = $data->quantity;

        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }

        return ($this->save());
    }

    public function doAddToEditHistory($old_data, $data){

        //set old values
        $old_data['outsourcing_water_name'] = $old_data['outsourcing_water']['name'];
        $old_data['outsourcing_driver_name'] = $old_data['outsourcing_driver']['name'];
        $old_data['quantity'] = $old_data['order']['quantity'];
        $old_data['delivery_at'] = Carbon::parse($old_data['delivery_at'])->format('Y-n-d');
     
        unset($old_data['id']); 
        unset($old_data['outsourcing_water_id']);   
        unset($old_data['outsourcing_driver_id']);
        unset($old_data['order_id']);  
        unset($old_data['status']);
        unset($old_data['outsourcing_water']);
        unset($old_data['outsourcing_driver']);
        unset($old_data['order']);

        $old_value = '';
        $old_value .= $old_data['outsourcing_water_name'] . ';';
        $old_value .= $old_data['outsourcing_driver_name'] . ';';
        $old_value .= $old_data['driver_name'] . ';';
        $old_value .= $old_data['quantity'] . ';';
        $old_value .= $old_data['delivery_at'];


        //set new values
        $new_value_obj = $data->toArray(); 
        $new_value_obj['outsourcing_water'] = OutsourcingWater::find($new_value_obj['outsourcing_water'])->name;
        $new_value_obj['outsourcing_driver'] = OutsourcingDriver::find($new_value_obj['outsourcing_driver'])->name;
        
        unset($new_value_obj['id']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        unset($new_value_obj['max_quantity']);

        $new_value = '';
        $new_value .= $new_value_obj['outsourcing_water'] . ';';
        $new_value .= $new_value_obj['outsourcing_driver'] . ';';
        $new_value .= $new_value_obj['driver_name'] . ';';
        $new_value .= $new_value_obj['quantity'] . ';';
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

        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);

        //recalculate inventory
        $empty_gallon->quantity -= ($this->order->quantity);
        $filled_gallon->quantity += ($this->order->quantity);

        //update order water and order data
        $this->status = 'selesai';
        $this->driver_name = $driver_name;
        $this->order->accepted_at = Carbon::now();

        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() ){
            return false;
        }

        return ($this->save()); 
    }

    public function doCancel(){

        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);

        //recalculate inventory
        $empty_gallon->quantity += ($this->order->quantity);
        $filled_gallon->quantity -= ($this->order->quantity);

        //update order water and order data
        $this->status = 'proses';
        $this->driver_name = null;
        $this->order->accepted_at = null;

        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() ){
            return false;
        }

        return ($this->save()); 
    }

    public function doConfirmWithIssue($driver_name){
        $this->status = 'bermasalah';
        $this->driver_name = $driver_name;
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

        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);

        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Kesalahan Pabrik Air"){
                    $broken_gallon->quantity -= $issue->quantity;
                    $filled_gallon->quantity += $issue->quantity;
                }
            }
        }

        $filled_gallon->quantity -= $this->order->quantity;
        $empty_gallon->quantity += $this->order->quantity;

        if($filled_gallon->quantity<0){
            $filled_gallon->quantity = 0;
        }

        if(!$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() || !$this->doAddToDeleteHistory($description, $author_id)){
            return false;
        }
        return $this->order->doDelete();
    }

    public function doAddToDeleteHistory($description, $author_id){
        $data = array(
            'module_name' => 'Order Water',
            'description' => $description,
            'data_id' => $this->order_id,
            'user_id' => $author_id
        );

        return DeleteHistory::create($data);
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
