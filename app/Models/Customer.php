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

        return ($this->save());
    }

    public function doUpdate($data)
    {
        $this->name = $data->name;
        $this->address = $data->address;
        $this->phone = $data->phone;

        return ($this->save());
    }

    public function doDelete(){
        return $this->delete();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }
}
