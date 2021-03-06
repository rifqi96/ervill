<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\ValidationException;

class OrderCustomerReturn extends Model
{
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }
    public function orderCustomerReturnInvoices(){
        return $this->hasMany('App\Models\OrderCustomerReturnInvoice');
    }

    public function author(){
        return $this->belongsTo('App\Models\User');
    }

    public function doMake($data, $author_id){
        $rent_qty = Customer::find($data->customer_id)->rent_qty;

        if(!$rent_qty || $rent_qty < 1){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('customer_gallon_rent_return', 'Customer tidak memiliki galon pinjam, mohon diperiksa kembali');
            throw new ValidationException($validator);
        }

        $returned_gallons = $data->empty_quantity + $data->filled_quantity;

        if($returned_gallons > $rent_qty){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('returned_gallons_return', 'Jumlah galon melebihi kapasitas, mohon diperiksa kembali');
            throw new ValidationException($validator);
        }

        //////////////validation finish/////////////

        $this->customer_id = $data->customer_id;
        $this->filled_gallon_quantity = $data->filled_quantity;
        $this->empty_gallon_quantity = $data->empty_quantity;
        if(!$data->is_refund){
            $this->is_non_refund = "true";
        }else{
            $this->is_non_refund = "false";
        }
        $this->description = $data->description;
        $this->return_at = Carbon::parse($data->return_at)->format('Y-n-d');
        $this->author_id = $author_id;
        //$this->status = 'Draft';

        $this->save();   

        //create nomor_faktur
     
        $re_header_invoice = (new ReHeaderInvoice())->doMake($this);
        if($this->empty_gallon_quantity>0){
            $empty = (new OrderCustomerReturnInvoice())->doMake($this, $re_header_invoice->id, "empty");
        }
        if($this->filled_gallon_quantity>0){
            $filled = (new OrderCustomerReturnInvoice())->doMake($this, $re_header_invoice->id, "filled");
        }
        
        

        return $this;
    }

    public function doConfirm(){
        $customer = Customer::find($this->customer_id);

        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);

        if(!$customer || !$customer->rent_qty || $customer->rent_qty < 1){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('customer', 'Gagal Retur, customer tidak memiliki galon pinjam');
            throw new ValidationException($validator);
        }

        $returned_gallons = $this->empty_gallon_quantity + $this->filled_gallon_quantity;

        if($returned_gallons > $customer->rent_qty){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('customer', 'Gagal Retur, jumlah galon retur melibihi kapasitas pinjam customer');
            throw new ValidationException($validator);
        }

        $empty_gallon->quantity += $this->empty_gallon_quantity;
        if(!$empty_gallon->save()){
            return false;
        }

        $filled_gallon->quantity += $this->filled_gallon_quantity;
        if(!$filled_gallon->save()){
            return false;
        }

        $outgoing_gallon->quantity -= $returned_gallons;
        if(!$outgoing_gallon->save()){
            return false;
        }

        $customer->rent_qty -= $returned_gallons;

        if(!$customer->save()){
            return false;
        }

        if($this->orderCustomerReturnInvoices[0]->reHeaderInvoice->status=="Batal"){
            //update faktur_detail
            $invoice_details = OrderCustomerReturnInvoice::where('order_customer_return_id',$this->id)->get();
            foreach ($invoice_details as $invoice_detail) {
                $invoice_detail->subtotal = $invoice_detail->quantity * Price::find($invoice_detail->price_id)->price;
                $invoice_detail->save();
            }
            $invoice_details[0]->reHeaderInvoice->payment_date = Carbon::now()->format('Y-n-d H:i:s');
            $invoice_details[0]->reHeaderInvoice->save();
        }

        //$this->status = 'Selesai';
        $this->orderCustomerReturnInvoices[0]->reHeaderInvoice->status = 'Selesai';
        $this->orderCustomerReturnInvoices[0]->reHeaderInvoice->save();
        return $this->save();
    }

    public function doCancel(){

        if($this->orderCustomerReturnInvoices[0]->reHeaderInvoice->status=="Selesai"){
            $customer = Customer::find($this->customer_id);

            $empty_gallon = Inventory::find(2);
            $filled_gallon = Inventory::find(3);
            $outgoing_gallon = Inventory::find(5);

            $returned_gallons = $this->empty_gallon_quantity + $this->filled_gallon_quantity;

            $customer->rent_qty += $returned_gallons;

            if(!$customer->save()){
                return false;
            }

            $empty_gallon->quantity -= $this->empty_gallon_quantity;
            if(!$empty_gallon->save()){
                return false;
            }

            $filled_gallon->quantity -= $this->filled_gallon_quantity;
            if(!$filled_gallon->save()){
                return false;
            }

            $outgoing_gallon->quantity += $returned_gallons;
            if(!$outgoing_gallon->save()){
                return false;
            }
        }
        

        //$this->status = 'Batal';

        $this->orderCustomerReturnInvoices[0]->reHeaderInvoice->status = 'Batal';
        $this->orderCustomerReturnInvoices[0]->reHeaderInvoice->save();

        


        return $this->save();
    }






    ///////api////////////
    // public function doStartShipment(){
    //     $this->status = 'Proses';
    //     return $this->save();
    // }
}
