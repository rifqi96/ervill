<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGallon extends Model
{
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

	public function doMake($order, $orderGallon)
    {        
        $this->outsourcing_driver_id = $orderGallon->outsourcing_driver;
        $this->order_id = $order->id;
        return ($this->save());
    }

    public function doUpdate($data)
    {              
        $empty_gallon = Inventory::find(1);
        $old_data = $this->toArray();

        //recalculate inventory if order is finished 
        if($this->order->accepted_at != null){
            $empty_gallon->quantity += ($data->quantity - $this->order->quantity);

            //set driver name
            $this->driver_name = $data->driver_name;
        }     

        //update order gallon and order data
        $this->outsourcing_driver_id = $data->outsourcing;         
        $this->order->quantity = $data->quantity;
        

        if(!$this->order->save() || !$empty_gallon->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }

        return ($this->save());
    }

    public function doAddToEditHistory($old_data, $data){

        //set old values
        $old_data['outsourcing_driver_name'] = $old_data['outsourcing_driver']['name'];
        $old_data['quantity'] = $old_data['order']['quantity'];
     
        unset($old_data['id']);    
        unset($old_data['outsourcing_driver_id']);
        unset($old_data['order_id']);  
        unset($old_data['outsourcing_driver']);
        unset($old_data['order']);

        $old_value = '';
        $old_value .= $old_data['outsourcing_driver_name'] . ';';
        $old_value .= $old_data['driver_name'] . ';';
        $old_value .= $old_data['quantity'];


        //set new values
        $new_value_obj = $data->toArray(); 
        $new_value_obj['outsourcing'] = OutsourcingDriver::find($new_value_obj['outsourcing'])->name;
        
        unset($new_value_obj['id']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);

        $new_value = '';
        $new_value .= $new_value_obj['outsourcing'] . ';';
        $new_value .= $new_value_obj['driver_name'] . ';';
        $new_value .= $new_value_obj['quantity'];

        $edit_data = array(
            'module_name' => 'Order Gallon',
            'data_id' => $data->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => auth()->id()
        );

        return EditHistory::create($edit_data);
    }

    public function doDelete($description, $author_id){

        $empty_gallon = Inventory::find(1);

        //recalculate inventory if order is finished
        if($this->order->accepted_at != null){
            $empty_gallon->quantity -= ($this->order->quantity);         
        }


        if($empty_gallon->quantity<0){
            $empty_gallon->quantity = 0;
        }

        if( !$empty_gallon->save() || !$this->doAddToDeleteHistory($description, $author_id)){
            return false;
        }
        return $this->order->doDelete();
    }

    public function doAddToDeleteHistory($description, $author_id){
        $data = array(
            'module_name' => 'Order Gallon',
            'description' => $description,
            'data_id' => $this->order_id,
            'user_id' => $author_id
        );

        return DeleteHistory::create($data);
    }

    public function doConfirm($driver_name){
        $this->driver_name = $driver_name;
        return ($this->save()); 
    }

    public function doCancel(){
        $this->driver_name = null;
        return ($this->save()); 
    }

    public function doRestore(){

        $empty_gallon = Inventory::find(1);

        //recalculate inventory if order is finished
        if($this->order->accepted_at != null){
            $empty_gallon->quantity += ($this->order->quantity);
        }

        if($empty_gallon->quantity<0){
            $empty_gallon->quantity = 0;
        }

        if(!$empty_gallon->save()){
            return false;
        }
        return $this->order->doDelete();
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function outsourcingDriver()
    {
        return $this->belongsTo('App\Models\OutsourcingDriver');
    }
}
