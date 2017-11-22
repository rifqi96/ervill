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
            ->whereHas('order', function ($query){
                $query->whereDate('created_at', '=', Carbon::today()->toDateString());
            })
            ->has('order')
            ->get();
    }

    public function doMake($order_data, $gallon_data, $customer_id)
    {
        $this->order_id = $order_data->id;
        $this->customer_id = $customer_id;
        $this->empty_gallon_quantity = 0;
        if($gallon_data->empty_gallon){
            $this->empty_gallon_quantity = $gallon_data->quantity;
        }
        $this->delivery_at = $gallon_data->delivery_at;
        $this->status = "Draft";
        return ($this->save());
    }

    public function doUpdate($data)
    {
        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);

        $filled_stock = (integer)$filled_gallon->quantity+$this->order->quantity;

        if($filled_stock < $data->quantity){
            return false;
        }

        $empty_gallon->quantity = ($empty_gallon->quantity - $this->empty_gallon_quantity) + $data->empty_gallon_quantity;
        $filled_gallon->quantity = ($filled_gallon->quantity + $this->order->quantity) - $data->quantity;

        $old_data = $this->toArray();

        $this->order->quantity = $data->quantity;
        $this->empty_gallon_quantity = $data->empty_gallon_quantity;
        $this->delivery_at = $data->delivery_at;
        $this->status = $data->status;
        $this->customer_id = $data->customer_id;

        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }
        return ($this->save());
    }

    public function doAddToEditHistory($old_data, $data){
        //set old values

        $old_data['customer_name'] = $old_data['customer']['name'];
        $old_data['quantity'] = $old_data['order']['quantity'];
        $old_data['delivery_at'] = Carbon::parse($old_data['delivery_at'])->format('Y-n-d');

        unset($old_data['id']);
        unset($old_data['shipment_id']);
        unset($old_data['customer_id']);
        unset($old_data['order_id']);
        unset($old_data['order']);
        unset($old_data['customer']);

        $old_value = '';
        $old_value .= $old_data['quantity'] . ';';
        $old_value .= $old_data['empty_gallon_quantity']. ';';
        $old_value .= $old_data['delivery_at']. ';';
        $old_value .= $old_data['customer_name']. ';';
        $old_value .= $old_data['status'];

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
        $new_value .= $new_value_obj['delivery_at']. ';';
        $new_value .= $new_value_obj['customer_name']. ';';
        $new_value .= $new_value_obj['status'];

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
        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Refund Gallon"){
                    $broken_gallon->quantity -= $issue->quantity;
                    $filled_gallon->quantity += $issue->quantity;
                }
                else{
                    $broken_gallon->quantity -= $issue->quantity;
                }
            }
        }

        $filled_gallon->quantity += $this->order->quantity;
        $empty_gallon->quantity -= $this->empty_gallon_quantity;

        if($empty_gallon->quantity<0){
            $empty_gallon->quantity = 0;
        }
        else if($broken_gallon->quantity<0){
            $broken_gallon->quantity = 0;
        }
        else if($filled_gallon->quantity<0){
            $filled_gallon->quantity = 0;
        }

        if(!$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() ||!$this->doAddToDeleteHistory($description, $author_id)){
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

        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Refund Gallon"){
                    $broken_gallon->quantity += $issue->quantity;
                    $filled_gallon->quantity -= $issue->quantity;
                }
                else{
                    $broken_gallon->quantity += $issue->quantity;
                }
            }
        }
        $filled_gallon->quantity -= $this->order->quantity;
        $empty_gallon->quantity += $this->empty_gallon_quantity;

        if($empty_gallon->quantity<0){
            $empty_gallon->quantity = 0;
        }
        else if($broken_gallon->quantity<0){
            $broken_gallon->quantity = 0;
        }
        else if($filled_gallon->quantity<0){
            $filled_gallon->quantity = 0;
        }

        if(!$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save()){
            return false;
        }

        return $this->order->doRestore();
    }
}
