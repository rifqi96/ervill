<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','full_name', 'email', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function doUpdateProfile($request)
    {
        $this->username = $request->username;
        $this->full_name = $request->full_name;
        $this->email = $request->email;
        $this->phone = $request->phone;

        return ($this->save());
    }

    public function doMake($request)
    {
        //check that admin only can create driver
        if(auth()->user()->role->name=='admin' && $request->role!=3)
            return false;

        $this->role_id = $request->role;
        $this->username = $request->username;
        $this->password = bcrypt($request->username);
        $this->full_name = $request->full_name;
        $this->email = $request->email;
        $this->phone = $request->phone;

        return ($this->save());
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    public function shipments()
    {
        return $this->hasMany('App\Models\Shipment');
    }
    
   
}
