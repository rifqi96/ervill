<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;
use App\Models\EditHistory;
use App\Models\DeleteHistory;
use Illuminate\Support\Collection;
use PhpParser\ErrorHandler\Collecting;

class OrderCustomer extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
    }

    public function getRecentOrders(){
        return $this->with([
            'shipment' => function($query){
                $query->with(['user']);
            },
            'customer',
            'order' => function($query){
                $query->with(['user', 'issues']);
            }
            ])
//            ->whereHas('order', function ($query){
//                $query->whereDate('created_at', '=', Carbon::today()->toDateString());
//            })
            ->has('order')
            ->whereDate('delivery_at', '=', Carbon::today()->toDateString())
            ->get();
    }

    public function doMake($gallon_data, $customer_id, $author_id)
    {
        $order_data = (new Order)->doMakeOrderCustomer($gallon_data, $author_id);

        if(!$order_data){
            return false;
        }

        $this->order_id = $order_data->id;
        $this->customer_id = $customer_id;
        $this->empty_gallon_quantity = 0;
        if($gallon_data->empty_gallon){
            $this->empty_gallon_quantity = $gallon_data->quantity;
        }
        $this->delivery_at = $gallon_data->delivery_at;
        $this->status = "Draft";

        return $this->save();
    }

    public function doUpdate($data)
    {
        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $outgoing_gallon = Inventory::find(4);

        $filled_stock = (integer)$filled_gallon->quantity+$this->order->quantity;

        if($filled_stock < $data->quantity){
            return false;
        }

        //check if empty_gallon_qty exceeds gallon_qty
        if($data->empty_gallon_quantity > $data->quantity){
            return false;
        }

        $empty_gallon->quantity = ($empty_gallon->quantity - $this->empty_gallon_quantity) + $data->empty_gallon_quantity;
        $filled_gallon->quantity = ($filled_gallon->quantity + $this->order->quantity) - $data->quantity;

        $outgoing_gallon_change = ($data->quantity - $this->order->quantity) - ($data->empty_gallon_quantity - $this->empty_gallon_quantity);
        $outgoing_gallon->quantity += $outgoing_gallon_change;

        $old_data = $this->toArray();

        $this->order->quantity = $data->quantity;
        $this->empty_gallon_quantity = $data->empty_gallon_quantity;
        if(!$this->shipment_id){
            $this->delivery_at = $data->delivery_at;
        }
        else if($data->remove_shipment){
            $this->shipment_id = null;
        }
        // $this->status = $data->status;
        $this->customer_id = $data->customer_id;

        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() || !$outgoing_gallon->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }
        return ($this->save());
    }

    public function doAddToEditHistory($old_data, $data){
        //set old values

        $old_data['customer_name'] = $old_data['customer']['name'];
        $old_data['quantity'] = $old_data['order']['quantity'];
        $old_data['delivery_at'] = Carbon::parse($old_data['delivery_at'])->format('Y-n-d');

        $isShipped = false;
        if($old_data['shipment_id']){
            $isShipped = true;
        }

        unset($old_data['id']);
        unset($old_data['shipment_id']);
        unset($old_data['customer_id']);
        unset($old_data['order_id']);
        unset($old_data['order']);
        unset($old_data['customer']);

        $old_value = '';
        $old_value .= $old_data['quantity'] . ';';
        $old_value .= $old_data['empty_gallon_quantity']. ';';
        if($isShipped){
            $old_value .= $old_data['customer_name'];
        }
        else{
            $old_value .= $old_data['delivery_at']. ';';
            $old_value .= $old_data['customer_name'];
        }

        //set new values
        $new_value_obj = $data->toArray();
        $new_customer = Customer::find($data->customer_id);
        $new_value_obj['customer_name'] = $new_customer->name;
        unset($new_value_obj['id']);
        unset($new_value_obj['customer-table_length']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        $new_value = '';
        $new_value .= $new_value_obj['quantity'] . ';';
        $new_value .= $new_value_obj['empty_gallon_quantity']. ';';
        if($isShipped){
            $new_value .= $new_value_obj['customer_name'];
        }
        else{
            $new_value .= $new_value_obj['delivery_at']. ';';
            $new_value .= $new_value_obj['customer_name'];
        }

        $edit_data = array(
            'module_name' => 'Order Customer',
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
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(4);

        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Refund Gallon"){
                    $broken_gallon->quantity -= $issue->quantity;
                    $filled_gallon->quantity += $issue->quantity;
                }
                else if($issue->type == "Kesalahan Customer" ){
                    $broken_gallon->quantity -= $issue->quantity;
                    $empty_gallon->quantity += $issue->quantity;
                }else if($issue->type == "Cancel Transaction"){
                    $empty_gallon->quantity += $this->empty_gallon_quantity;
                    $filled_gallon->quantity -= $issue->quantity;
                    $outgoing_gallon->quantity += ($issue->quantity - $this->empty_gallon_quantity);
                }
            }
        }

        $filled_gallon->quantity += $this->order->quantity;
        $empty_gallon->quantity -= $this->empty_gallon_quantity;
        $outgoing_gallon->quantity -= ($this->order->quantity - $this->empty_gallon_quantity);

        // if($empty_gallon->quantity<0){
        //     $empty_gallon->quantity = 0;
        // }
        // else if($broken_gallon->quantity<0){
        //     $broken_gallon->quantity = 0;
        // }
        // else if($filled_gallon->quantity<0){
        //     $filled_gallon->quantity = 0;
        // }

        if(!$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() || !$outgoing_gallon->save() ||!$this->doAddToDeleteHistory($description, $author_id)){
            return false;
        }
        return $this->order->doDelete();
    }

    public function doAddToDeleteHistory($description, $author_id){
        $data = array(
            'module_name' => 'Order Customer',
            'description' => $description,
            'data_id' => $this->order_id,
            'user_id' => $author_id
        );

        return DeleteHistory::create($data);
    }

    public function doRestore(){
        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(4);

        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Refund Gallon"){
                    $broken_gallon->quantity += $issue->quantity;
                    $filled_gallon->quantity -= $issue->quantity;
                }
                else if($issue->type == "Kesalahan Customer" ){
                    $broken_gallon->quantity += $issue->quantity;
                    $empty_gallon->quantity -= $issue->quantity;
                }else if($issue->type == "Cancel Transaction"){
                    $empty_gallon->quantity -= $this->empty_gallon_quantity;
                    $filled_gallon->quantity += $issue->quantity;
                    $outgoing_gallon->quantity -= ($issue->quantity - $this->empty_gallon_quantity);
                }
            }
        }
        $filled_gallon->quantity -= $this->order->quantity;
        $empty_gallon->quantity += $this->empty_gallon_quantity;
        $outgoing_gallon->quantity += ($this->order->quantity - $this->empty_gallon_quantity);

        // if($empty_gallon->quantity<0){
        //     $empty_gallon->quantity = 0;
        // }
        // else if($broken_gallon->quantity<0){
        //     $broken_gallon->quantity = 0;
        // }
        // else if($filled_gallon->quantity<0){
        //     $filled_gallon->quantity = 0;
        // }

        if( !$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() || !$outgoing_gallon->save() ){
            return false;
        }

        return $this->order->doRestore();
    }

    public function doForceDelete(){
        if(!$this->delete()){
            return false;
        }

        return $this->order->forceDelete();
    }

    public function doStartShipment(){
        $this->status = 'Proses';
        return $this->save();
    }

    public function doDropGallon(){

        if( count($this->order->issues) > 0 ){
            $this->status = 'Bermasalah';
        }else{
            $this->status = 'Selesai';
        }        
        return $this->save();
    }

    public function doEditOrder($data)
    {
        //check if quantity is integer or not
        if( filter_var($data->gallon_qty, FILTER_VALIDATE_INT)===false || filter_var($data->empty_gallon_qty, FILTER_VALIDATE_INT)===false ){
            return false;
        }

        //check if quantity less than 0 or not
        if( filter_var($data->gallon_qty, FILTER_VALIDATE_INT)<0 || filter_var($data->empty_gallon_qty, FILTER_VALIDATE_INT)<0 ){
            return false;
        }

        //check if empty_gallon_qty exceeds gallon_qty
        if($data->empty_gallon_qty > $data->gallon_qty){
            return false;
        }

        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $outgoing_gallon = Inventory::find(4);

        $filled_stock = (integer)$filled_gallon->quantity+$this->order->quantity;

        if($filled_stock < $data->gallon_qty){
            return false;
        }

        $empty_gallon->quantity = ($empty_gallon->quantity - $this->empty_gallon_quantity) + $data->empty_gallon_qty;
        $filled_gallon->quantity = ($filled_gallon->quantity + $this->order->quantity) - $data->gallon_qty;

        $outgoing_gallon_change = ($data->gallon_qty - $this->order->quantity) - ($data->empty_gallon_qty - $this->empty_gallon_quantity);
        $outgoing_gallon->quantity += $outgoing_gallon_change;

        $old_data = $this->toArray();

        $this->order->quantity = $data->gallon_qty;
        $this->empty_gallon_quantity = $data->empty_gallon_qty;


        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() || !$outgoing_gallon->save() || !$this->doAddToEditHistoryApi($old_data, $data, $this->id)){
            return false;
        }
        return ($this->save());
    }

    public function doAddToEditHistoryApi($old_data, $data, $order_customer_id){
        //set old values

        $old_data['quantity'] = $old_data['order']['quantity'];

        unset($old_data['id']);
        unset($old_data['shipment_id']);
        unset($old_data['customer_id']);
        unset($old_data['order_id']);
        unset($old_data['order']);
        unset($old_data['customer']);

        $old_value = '';
        $old_value .= $old_data['quantity'] . ';';
        $old_value .= $old_data['empty_gallon_quantity'];
      

        //set new values
        $new_value_obj = $data->toArray();

        unset($new_value_obj['id']);
        unset($new_value_obj['customer-table_length']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);

        $new_value = '';
        $new_value .= $new_value_obj['gallon_qty'] . ';';
        $new_value .= $new_value_obj['empty_gallon_qty'];
       

        $edit_data = array(
            'module_name' => 'Order Customer by Driver',
            'data_id' => $order_customer_id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => $data->user_id
        );

        return EditHistory::create($edit_data);
    }

    //test
    public function doUpdateStatus($status){
        $this->status = $status;
        return $this->save();
    }
}
