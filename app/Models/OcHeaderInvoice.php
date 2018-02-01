<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcHeaderInvoice extends Model
{
    protected $guarded = [];

    public function orderCustomerInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerInvoice');
    }
    public function orderCustomerBuyInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerBuyInvoice');
    }

    public function doMake($data){

    	$id = OcHeaderInvoice::orderBy('id','desc')->pluck('id')->first();
    	if($id){
    		$id = (string)((int)substr($id,2)+1);
            while( strlen($id) < 7 ){
                $id = '0'.$id;
            }
            $this->id = 'OC'.$id;
    	}else{
    		$this->id = 'OC0000001';
    	}
    	
    	if($data->xxx){
    		$this->payment_status = "piutang";
    	}else{
    		$this->payment_status = "cash";
    	}

    	if($data->xxx){
    		$this->is_free = "true";
    	}else{
    		$this->is_free = "false";
    	}

    	$this->save();
    	return $this;
    }
}
