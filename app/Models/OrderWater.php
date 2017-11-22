<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function doUpdate($orderWater)
    {      
        $this->outsourcing_water_id = $orderWater->outsourcing_water;
        $this->outsourcing_driver_id = $orderWater->outsourcing_driver;
        if($this->order->accepted_at != null){
            $this->driver_name = $orderWater->driver_name;
        }
        $this->delivery_at = $orderWater->delivery_at;

        return ($this->save());
    }

    public function doConfirm($driver_name){
        $this->status = 'selesai';
        $this->driver_name = $driver_name;
        return ($this->save()); 
    }

    public function doCancel(){
        $this->status = 'proses';
        $this->driver_name = null;
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

        if($empty_gallon->quantity<0){
            $empty_gallon->quantity = 0;
        }
        else if($broken_gallon->quantity<0){
            $broken_gallon->quantity = 0;
        }
        else if($filled_gallon->quantity<0){
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
