<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function issues()
    {
        return $this->belongsToMany('App\Models\Issue');
    }
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
    }
}
