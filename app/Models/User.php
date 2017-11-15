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

    public function __construct($user=null){
        if ($user !=null){
            $this->doMake($user);
        }
    }

    public function doUpdateProfile($user)
    {
        $this->username = $user->username;
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->phone = $user->phone;

        return ($this->save());
    }

    public function doMake($user)
    {
        //check that admin only can create driver
        if(auth()->user()->role->name=='admin' && $user->role!=3)
            return false;

        $this->role_id = $user->role;
        $this->username = $user->username;
        $this->password = bcrypt($user->username);
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->phone = $user->phone;

        return ($this->save());
    }

    public function doUpdate($user)
    {
        $this->role_id = $user->role;
        $this->username = $user->username;
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->phone = $user->phone;

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
