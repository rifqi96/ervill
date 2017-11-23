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

    public function doMakeOrderGallon($data, $author_id)
    {        
        $this->inventory_id = 1;
        $this->user_id = $author_id;
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

        $this->save();
        return $this;
    }


    public function doDelete(){
        return $this->delete();
    }

    public function doRestore(){
        return $this->restore();
    }

    public function doForceDelete(){
        return $this->forceDelete();
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
