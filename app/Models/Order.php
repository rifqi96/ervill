<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function doMakeOrderGallon($order)
    {        
        $this->inventory_id = 1;
        $this->user_id = auth()->id();
        $this->quantity = $order->quantity;
        return ($this->save());
    }

    public function doMakeOrderWater($order)
    {        
        $this->inventory_id = 2;
        $this->user_id = auth()->id();
        $this->quantity = $order->quantity;
        return ($this->save());
    }

    public function doUpdateOrderGallon($order){
        $this->quantity = $order->quantity;
        return $this->save();
    }

    public function doDelete(){
        return $this->delete();
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
