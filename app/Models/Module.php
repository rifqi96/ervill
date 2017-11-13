<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['id', 'name'];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role','module_accesses');
    }
}
