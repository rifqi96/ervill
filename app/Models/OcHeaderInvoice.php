<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\ValidationException;

class OcHeaderInvoice extends Model
{
    protected $guarded = [];
    
    //for non-auto-increment PK
    protected $primaryKey = 'id';
    public $incrementing = false;

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
    	
    	if($data->is_piutang){
    		$this->payment_status = "piutang";
    	}else{
    		$this->payment_status = "cash";
            $this->payment_date = Carbon::now()->format('Y-n-d H:i:s');
    	}

    	if($data->is_free){
    		$this->is_free = "true";
            $this->payment_date = Carbon::now()->format('Y-n-d H:i:s');
    	}else{
    		$this->is_free = "false";
    	}

    	$this->save();
    	return $this;
    }

    public function doPay(){
        if($this->payment_status == "cash"){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('id', 'Gagal update, status faktur telah lunas');
            throw new ValidationException($validator);
        }

        $this->payment_date = Carbon::now()->format('Y-n-d H:i:s');
        $this->payment_status = 'cash';

        return $this->save();
    }
}
