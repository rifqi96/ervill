<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReHeaderInvoice extends Model
{
    protected $guarded = [];

    //for non-auto-increment PK
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function orderCustomerReturnInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerReturnInvoice');
    }

    public function doMake($data){

    	$id = ReHeaderInvoice::orderBy('id','desc')->pluck('id')->first();
    	if($id){
    		$id = (string)((int)substr($id,2)+1);
            while( strlen($id) < 7 ){
                $id = '0'.$id;
            }
            $this->id = 'RE'.$id;
    	}else{
    		$this->id = 'RE0000001';
    	}

    	$this->payment_status = "Refund";
        $this->payment_date = Carbon::now()->format('Y-n-d H:i:s');
    	if($data->is_non_refund=="true"){
    		$this->payment_status = "Non Refund";
    		
    	}   	

    	

    	$this->save();
    	return $this;
    }
}
