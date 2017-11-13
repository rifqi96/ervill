<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['id', 'name'];


    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
    public function modules()
    {
        return $this->belongsToMany('App\Models\Module','module_accesses');
    }
}
