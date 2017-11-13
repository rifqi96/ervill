<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    public function issues()
    {
        return $this->hasMany('App\Models\Issue');
    }
}
