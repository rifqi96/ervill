<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','address', 'phone'
    ];

    // Relations //
    public function order_customers(){
        return $this->hasMany('App\Models\OrderCustomer');
    }

    public function doMake($data)
    {
        $this->name = $data->name;
        $this->address = $data->address;
        $this->phone = $data->phone;
        $this->gallon_quantity = 0;

        return ($this->save());
    }

    public function doUpdate($data)
    {
        //retur galon
        $outgoing_gallon = Inventory::find(4);
        $outgoing_gallon->quantity += ($data->gallon_quantity - $this->gallon_quantity);
        $outgoing_gallon->save();

        $empty_gallon = Inventory::find(1);
        $empty_gallon->quantity -= ($data->gallon_quantity - $this->gallon_quantity);
        $empty_gallon->save();

        $this->name = $data->name;
        $this->address = $data->address;
        $this->phone = $data->phone;
        $this->gallon_quantity = $data->gallon_quantity;



        return ($this->save());
    }

    public function doRestore(){
        return $this->restore();
    }

    public function doDelete(){
        return $this->delete();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }
}
