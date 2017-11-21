<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
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
        'user_id','status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function orderCustomers()
    {
        return $this->hasMany('App\Models\OrderCustomer');
    }
}
