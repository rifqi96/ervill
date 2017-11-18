<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    public function doMakeOrderGallon($order)
    {        
        $this->inventory_id = 1;
        $this->user_id = auth()->id();
        $this->quantity = $order->quantity;
        $this->created_at = Carbon::now();
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
