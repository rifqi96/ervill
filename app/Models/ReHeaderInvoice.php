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
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
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

    	$this->status="Draft";

    	$this->save();
    	return $this;
    }

    public function setReturnAttributes(){
        $this->has_order = false;
        $this->filled_gallon = 0;
        $this->empty_gallon = 0;
        $this->status = 'Selesai';
        if($this->orderCustomerReturnInvoices->count() > 0){
            $this->has_order = true;
            if($this->orderCustomerReturnInvoices[0]->orderCustomerReturn && $this->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer){
                $this->delivery_at = $this->orderCustomerReturnInvoices[0]->orderCustomerReturn->return_at;
                $this->customer_id = $this->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->id;
                $this->customer_name = $this->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->name;
                $this->customer_address = $this->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->address;
                $this->customer_phone = $this->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->phone;

                foreach($this->orderCustomerReturnInvoices as $ocReturn){
                    $this->filled_gallon += $ocReturn->orderCustomerReturn->filled_gallon_quantity;
                    $this->empty_gallon += $ocReturn->orderCustomerReturn->empty_gallon_quantity;
                    $this->status = $ocReturn->orderCustomerReturn->status;
                }
            }
        }
    }

    //////////api/////////
    public function doStartShipment(){
        $this->status = 'Proses';
        return $this->save();
    }
    public function doDropGallon(){

        // if( count($this->order->issues) > 0 ){
        //     $this->status = 'Bermasalah';
        // }else{
        //     $this->status = 'Selesai';
        // }   

        if(!$this->orderCustomerReturnInvoices[0]->orderCustomerReturn->doConfirm()){
            return false;
        }

        $this->status = 'Selesai';  
        return $this->save();
    }
}
