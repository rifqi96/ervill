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
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
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

        $this->status = "Draft";

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

    public function scopeIsPaid($query){
        return $query->where([
            ['payment_status', '=', 'cash'],
            ['is_free', '=', 'false']
        ]);
    }

    public function scopeIsPiutang($query){
        return $query->where([
            ['payment_status', '=', 'piutang'],
            ['is_free', '=', 'false']
        ]);
    }

    public function scopeIsFree($query){
        return $query->where([
            ['is_free', '=', 'true']
        ]);
    }

    public function setInvoiceAttributes(){
        $this->has_order = false;
        $this->filled_gallon = 0;
        $this->ervill_empty_gallon = 0;
        $this->non_ervill_empty_gallon = 0;
        if($this->orderCustomerInvoices->count() > 0){
            $this->has_order = true;
            $this->is_only_buy = false;
            if($this->orderCustomerInvoices[0]->orderCustomer && $this->orderCustomerInvoices[0]->orderCustomer->customer){
                $this->delivery_at = $this->orderCustomerInvoices[0]->orderCustomer->delivery_at;
                $this->customer_id = $this->orderCustomerInvoices[0]->orderCustomer->customer->id;
                $this->customer_name = $this->orderCustomerInvoices[0]->orderCustomer->customer->name;
                $this->customer_address = $this->orderCustomerInvoices[0]->orderCustomer->customer->address;
                $this->customer_phone = $this->orderCustomerInvoices[0]->orderCustomer->customer->phone;

                foreach($this->orderCustomerInvoices as $ocInvoice){
                    //Filled Gallon
                    if($ocInvoice->price_id != 5 || $ocInvoice->price_id != 6 || $ocInvoice->price_id != 7 || $ocInvoice->price_id != 12 || $ocInvoice->price_id != 13 || $ocInvoice->price_id != 14){
                        $this->filled_gallon += $ocInvoice->quantity;
                    }

                    // Ervill Empty Gallon
                    if($ocInvoice->price_id == 1 || $ocInvoice->price_id == 8){
                        $this->ervill_empty_gallon += $ocInvoice->quantity;
                    }

                    // Non Ervill Empty Gallon
                    if($ocInvoice->price_id == 3 || $ocInvoice->price_id == 10){
                        $this->non_ervill_empty_gallon += $ocInvoice->quantity;
                    }
                }
            }
        }
        else if($this->orderCustomerInvoices->count() < 1 && $this->orderCustomerBuyInvoices->count() > 0){
            $this->has_order = true;
            $this->is_only_buy = true;
            if($this->orderCustomerBuyInvoices[0]->orderCustomerBuy->customer){
                $this->delivery_at = $this->orderCustomerBuyInvoices[0]->orderCustomerBuy->buy_at;
                $this->customer_id = $this->orderCustomerBuyInvoices[0]->orderCustomerBuy->customer->id;
                $this->customer_name = $this->orderCustomerBuyInvoices[0]->orderCustomerBuy->customer->name;
                $this->customer_address = $this->orderCustomerBuyInvoices[0]->orderCustomerBuy->customer->address;
                $this->customer_phone = $this->orderCustomerBuyInvoices[0]->orderCustomerBuy->customer->phone;
            }
        }

        $this->status = "LUNAS";

        if($this->payment_status == "piutang"){
            $this->status = "PIUTANG";
        }
        else if($this->is_free == "true"){
            $this->status = "FREE atau SAMPLE";
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
        if(count($this->orderCustomerBuyInvoices)>0){
            // if(!$this->orderCustomerBuyInvoices[0]->orderCustomerBuy->doConfirm()){//salah, kalo ada 2 order yang menunjuk ke satu faktur
            //     return false;
            // }

            foreach ($this->orderCustomerBuyInvoices as $orderCustomerBuyInvoice) {
                if(!$orderCustomerBuyInvoice->orderCustomerBuy->doConfirm()){
                    return false;
                }
            }
            
        }
        $this->status = 'Selesai';  
        return $this->save();
    }

    public function doCancelTransaction(){
          
        if(count($this->orderCustomerInvoices)>0){
            if(!$this->orderCustomerInvoices[0]->orderCustomer->doCancel()){/////masih salah yang nomor faktur klo 2 order
                return false;
            }

            // foreach ($this->orderCustomerInvoices as $orderCustomerInvoice) {
            //     if(!$orderCustomerInvoice->orderCustomer->doCancel()){
            //         return false;
            //     }
            // }
            
        }
        if(count($this->orderCustomerBuyInvoices)>0){
            // if(!$this->orderCustomerBuyInvoices[0]->orderCustomerBuy->doCancel()){
            //     return false;
            // }

            foreach ($this->orderCustomerBuyInvoices as $orderCustomerBuyInvoice) {
                if(!$orderCustomerBuyInvoice->orderCustomerBuy->doCancel()){
                    return false;
                }
            }
            
        }
        $this->status = 'Batal';  
        return $this->save();
    }
}
