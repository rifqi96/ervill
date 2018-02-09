<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\ValidationException;

class OrderCustomerBuy extends Model
{
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }

    public function author(){
        return $this->belongsTo('App\Models\User');
    }
    public function orderCustomerBuyInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerBuyInvoice');
    }

    public function doMake($data, $author_id){
        $customer_gallon_rent = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $data->customer_id],
                ['type', 'rent']
            ])
            ->first();

        $customer_gallon_purchase = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $data->customer_id],
                ['type', 'purchase']
            ])
            ->first();

        if(!$customer_gallon_rent || $customer_gallon_rent->qty < $data->quantity){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('customer_gallon_rent', 'Jumlah galon melebihi kapasitas, mohon diperiksa kembali');
            throw new ValidationException($validator);
            //return false;
        }

        if($data->change_nomor_struk){

            //check whether invalid nomor_struk
            $oc_struk = OrderCustomer::whereHas('orderCustomerInvoices',function($query) use($data){
                $query->where('oc_header_invoice_id',$data->nomor_struk);
            })
            ->where([
                ['customer_id',$data->customer_id],
                ['status','Draft'],
                ['delivery_at',$data->buy_at]
            ])->get();

            if(count($oc_struk)==0){
                $validator = Validator::make([], []); // Empty data and rules fields
                $validator->errors()->add('nomor_struk', 'Input nomor faktur salah, mohon diperiksa kembali');
                throw new ValidationException($validator);
                //return false;
            }
        }   

        //////validation finish///////////               

        $this->customer_id = $data->customer_id;
        
        $this->quantity = $data->quantity;
        $this->author_id = $author_id;
        $this->buy_at = Carbon::parse($data->buy_at)->format('Y-n-d');
        //$this->status = "Draft";
        $this->save();

        if($data->change_nomor_struk){            
            $orderCustomerBuyInvoice = (new OrderCustomerBuyInvoice())->doMake($this, $data->nomor_struk);
            //$this->no_struk = $data->nomor_struk;
            //refill and add gallon
            // if($this->purchase_type && $this->is_new=="false" && $this->order->quantity!=0){
            //     $orderCustomerBuyInvoice = (new OrderCustomerBuyInvoice())->doMake($this, $data->nomor_struk, true);
            // }
        }else{
            $oc_header_invoice = (new OcHeaderInvoice())->doMake($data);
            $orderCustomerBuyInvoice = (new OrderCustomerBuyInvoice())->doMake($this, $oc_header_invoice->id);
            //$this->no_struk = $oc_header_invoice->id;
            //refill and add gallon
            // if($this->purchase_type && $this->is_new=="false" && $this->order->quantity!=0){
            //     $orderCustomerBuyInvoice = (new OrderCustomerBuyInvoice())->doMake($this, $oc_header_invoice->id, true);
            // }
        }

        //$this->no_struk = $data->nomor_struk?$data->nomor_struk:$oc_header_invoice->id;

        return $this;
    }

    public function doConfirm(){

        $customer_gallon_rent = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'rent']
            ])
            ->first();

        $customer_gallon_purchase = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'purchase']
            ])
            ->first();

        if(!$customer_gallon_rent || $customer_gallon_rent->qty < $this->quantity){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('customer_gallon_rent', 'Jumlah galon melebihi kapasitas, mohon diperiksa kembali');
            throw new ValidationException($validator);
            //return false;
        }

        $customer_gallon_rent->qty -= $this->quantity;
        if($customer_gallon_rent->qty<1){
            $customer_gallon_rent->delete();
        }else{
            $customer_gallon_rent->save();
        }
        if(!$customer_gallon_purchase){
            $data = (object)array();
            $data->purchase_type = "purchase"; 
            $data->quantity = $this->quantity;         
            if(!(new CustomerGallon)->doMake($data, $this->customer_id)){
                return false;
            }
        }
        else{
            $customer_gallon_purchase->qty += $this->quantity;
            if(!$customer_gallon_purchase->save()){
                return false;
            }
        }


        

        $outgoing_gallon = Inventory::find(5);
        $sold_gallon = Inventory::find(7);

        if(!$outgoing_gallon || !$sold_gallon){
            return false;
        }

        $outgoing_gallon->quantity -= $this->quantity;
        $sold_gallon->quantity += $this->quantity;

        if(!$outgoing_gallon->save() || !$sold_gallon->save()){
            return false;
        }

        if($this->orderCustomerBuyInvoices[0]->ocHeaderInvoice->status=="Batal"){
            //update faktur_detail
            $invoice_details = OrderCustomerBuyInvoice::where('order_customer_buy_id',$this->id)->get();
            foreach ($invoice_details as $invoice_detail) {
                $invoice_detail->subtotal = $invoice_detail->quantity * Price::find($invoice_detail->price_id)->price;
                $invoice_detail->save();
            }
            $invoice_details[0]->ocHeaderInvoice->payment_date = Carbon::now()->format('Y-n-d H:i:s');
            $invoice_details[0]->ocHeaderInvoice->save();
        }



        $this->orderCustomerBuyInvoices[0]->ocHeaderInvoice->status = 'Selesai';
        $this->orderCustomerBuyInvoices[0]->ocHeaderInvoice->save();
        return $this->save();
    }

    public function doCancel(){
        $customer_gallon_rent = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'rent']
            ])
            ->first();

        $customer_gallon_purchase = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'purchase']
            ])
            ->first();

        if(!$customer_gallon_purchase){
            return false;
        }

        if(!$customer_gallon_rent){
            $customer_gallon_rent = new CustomerGallon();
            $customer_gallon_rent->customer_id = $this->customer_id;
            $customer_gallon_rent->qty = $this->quantity;
            $customer_gallon_rent->type="rent";
        }else{
            $customer_gallon_rent->qty += $this->quantity;
        }
        
        $customer_gallon_purchase->qty -= $this->quantity;

        if(!$customer_gallon_rent->save() || !$customer_gallon_purchase->save()){
            return false;
        }

        $outgoing_gallon = Inventory::find(5);
        $sold_gallon = Inventory::find(7);

        if(!$outgoing_gallon || !$sold_gallon){
            return false;
        }

        $outgoing_gallon->quantity += $this->quantity;
        $sold_gallon->quantity -= $this->quantity;

        if(!$outgoing_gallon->save() || !$sold_gallon->save()){
            return false;
        }

        
        $this->orderCustomerBuyInvoices[0]->ocHeaderInvoice->status = 'Batal';
        $this->orderCustomerBuyInvoices[0]->ocHeaderInvoice->save();
        return $this->save();
    }

    public function doDelete(){
        $customer_gallon_rent = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'rent']
            ])
            ->first();

        $customer_gallon_purchase = CustomerGallon::with([
            'customer'
        ])
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'purchase']
            ])
            ->first();

        if(!$customer_gallon_purchase){
            return false;
        }

        if(!$customer_gallon_rent){
            $customer_gallon_rent = new CustomerGallon();
            $customer_gallon_rent->customer_id = $this->customer_id;
            $customer_gallon_rent->qty = $this->quantity;
            $customer_gallon_rent->type="rent";
        }else{
            $customer_gallon_rent->qty += $this->quantity;
        }
        
        $customer_gallon_purchase->qty -= $this->quantity;

        if(!$customer_gallon_rent->save() || !$customer_gallon_purchase->save()){
            return false;
        }

        $outgoing_gallon = Inventory::find(5);
        $sold_gallon = Inventory::find(7);

        if(!$outgoing_gallon || !$sold_gallon){
            return false;
        }

        $outgoing_gallon->quantity += $this->quantity;
        $sold_gallon->quantity -= $this->quantity;

        if(!$outgoing_gallon->save() || !$sold_gallon->save()){
            return false;
        }

        //delete oc_buy_invoice_detail
        $oc_buy_invoice_detail = OrderCustomerBuyInvoice::where('order_customer_buy_id',$this->id)->first();
        $oc_buy_invoice_detail->delete();

//        $data = array(
//            'module_name' => 'Pindah Tangan Galon',
//            'description' => $data->description,
//            'data_id' => $data->id,
//            'user_id' => $author_id
//        );
//
//        if(!DeleteHistory::create($data)){
//            return false;
//        }

        return $this->delete();
    }


    /////////////api///////////////
    // public function doStartShipment(){
    //     $this->status = 'Proses';
    //     return $this->save();
    // }
}
