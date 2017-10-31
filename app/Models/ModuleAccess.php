<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleAccess extends Model
{
    protected $fillable = ['module_id', 'role_id'];

    public function modules(){
        return $this->belongsTo('App\Models\Module');
    }

    public function roles(){
        return $this->belongsTo('App\Models\Role');
    }
}
