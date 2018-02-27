<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CustomerNonErvill extends Model
{
	use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
	
    protected $guarded = [];

    // Relations //
    public function neHeaderInvoices(){
        return $this->hasMany('App\Models\NeHeaderInvoice');
    }

    public function doMake($data)
    {
        $this->name = $data->name;
        $this->address = $data->address;
        $this->phone = $data->phone;  
        $this->aqua_qty = 0;
        $this->non_aqua_qty = 0;    
       
        
        $this->save();        

        return $this;
    }

    public function doUpdate($data)
    {
        $this->name = $data->name;
        $this->address = $data->address;
        $this->phone = $data->phone;
        

        return ($this->save());
    }

    public function doUpdateGallons($data){
        if(isset($data->aqua_qty) && $data->aqua_qty > 0){
            $this->aqua_qty += $data->aqua_qty;
        }

        if(isset($data->non_aqua_qty) && $data->non_aqua_qty > 0){
            $this->non_aqua_qty += $data->non_aqua_qty;
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
}
