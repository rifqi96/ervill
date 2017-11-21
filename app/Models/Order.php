<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory;
use App\Models\Issue;

class Order extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function doMakeOrderGallon($data)
    {        
        $this->inventory_id = 1;
        $this->user_id = auth()->id();
        $this->quantity = $data->quantity;
        return ($this->save());
    }

    public function doMakeOrderWater($order)
    {        
        $this->inventory_id = 2;
        $this->user_id = auth()->id();
        $this->quantity = $order->quantity;
        return ($this->save());
    }

    public function doMakeOrderCustomer($data){
        $this->inventory_id = 2;
        $this->user_id = auth()->id();
        $this->quantity = $data->quantity;

        $filled_gallon = Inventory::find(2);
        $filled_gallon->quantity -= $data->quantity;
        if(!$filled_gallon->save()){
            return false;
        }

        if($data->empty_gallon){
            $empty_gallon = Inventory::find(1);
            $empty_gallon->quantity += $data->quantity;
            if(!$empty_gallon->save()){
                return false;
            }
        }

        return $this->save();
    }

    public function doUpdate($order){
        $this->quantity = $order->quantity;
        return $this->save();
    }

    public function doDelete(){
        return $this->delete();
    }

    public function doRestore(){
        $empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);
        if($this->inventory_id == 2){
            // If restore order customer

            if($this->issues){
                foreach($this->issues as $issue){
                    if($issue->type == "Refund Gallon"){
                        $broken_gallon->quantity += $issue->quantity;
                        $filled_gallon->quantity -= $issue->quantity;
                    }
                    else{
                        $broken_gallon->quantity += $issue->quantity;
                    }
                }
            }
            $filled_gallon->quantity -= $this->quantity;
            $empty_gallon->quantity += $this->orderCustomer->empty_gallon_quantity;

            if(!$filled_gallon->save()){
                return false;
            }
            else if(!$empty_gallon->save()){
                return false;
            }
        }

        return $this->restore();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }

    public function doConfirm(){
        $this->accepted_at = Carbon::now();
        return ($this->save()); 
    }

    public function doCancel(){
        $this->accepted_at = null;
        return ($this->save()); 
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    }
    public function issues(){
        return $this->hasMany('App\Models\Issue');
    }
    public function orderGallon()
    {
        return $this->hasOne('App\Models\OrderGallon');
    }
    public function orderWater()
    {
        return $this->hasOne('App\Models\OrderWater');
    }
    public function orderCustomer()
    {
        return $this->hasOne('App\Models\OrderCustomer');
    }
}
