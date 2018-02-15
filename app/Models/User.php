<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
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

    public function doUpdateProfile($user)
    {
        $this->username = $user->username;
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->phone = $user->phone;

        if($user->change_password){
            $this->password = bcrypt($user->password);
        }

        return ($this->save());
    }

    public function doMake($user)
    {
        //check that admin only can create driver
        if(auth()->user()->role->name=='admin' && $user->role!=3)
            return false;

        $this->role_id = $user->role;
        $this->username = $user->username;
        $this->password = bcrypt($user->password);
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        
        return ($this->save());
    }

    public function doUpdate($user)
    {
        if(auth()->user()->role->name=='admin' && $user->role!=3)
            return false;
        
        $this->role_id = $user->role;
        $this->username = $user->username;
        $this->full_name = $user->full_name;
        $this->email = $user->email;
        $this->phone = $user->phone;

        if($user->change_password){
            $this->password = bcrypt($user->password);
        }

        
        return $this->save();
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

    public function doUpdateApiToken($token){
        $this->ervill_token = $token;
        return $this->save();
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
    public function delete_histories()
    {
        return $this->hasMany('App\Models\DeleteHistory');
    }
    public function editHistories()
    {
        return $this->hasMany('App\Models\EditHistory');
    }
    public function userThirdParty() {
        return $this->hasOne('App\Models\UserThirdParty');
    }
    public function ocHeaderInvoices() {
        return $this->hasMany('App\Models\OcHeaderInvoice');
    }

}
