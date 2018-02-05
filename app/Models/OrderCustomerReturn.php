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
        $customer_gallon = CustomerGallon::with([
            'customer'
        ])
        ->where([
            ['customer_id', $data->customer_id],
            ['type', 'rent']
        ])
            ->first();

        if(!$customer_gallon){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('customer_gallon_rent_return', 'Customer tidak memiliki galon pinjam, mohon diperiksa kembali');
            throw new ValidationException($validator);
        }

        $returned_gallons = $data->empty_quantity + $data->filled_quantity;

        if($returned_gallons > $customer_gallon->qty){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('returned_gallons_return', 'Jumlah galon melebihi kapasitas, mohon diperiksa kembali');
            throw new ValidationException($validator);
        }

        //////////////validation finish/////////////

        $this->customer_id = $data->customer_id;
        $this->filled_gallon_quantity = $data->filled_quantity;
        $this->empty_gallon_quantity = $data->empty_quantity;
        if($data->is_non_refund){
            $this->is_non_refund = "true";
        }else{
            $this->is_non_refund = "false";
        }
        $this->description = $data->description;
        $this->return_at = Carbon::parse($data->return_at)->format('Y-n-d');
        $this->author_id = $author_id;
        $this->status = 'Draft';

        $this->save();        
        

        return $this;
    }

    public function doConfirm(){
        $customer_gallon = CustomerGallon::with('customer')
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'rent']
            ])
            ->first();

        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);

        if(!$customer_gallon){
            return false;
        }

        $returned_gallons = $this->empty_gallon_quantity + $this->filled_gallon_quantity;

        if($returned_gallons > $customer_gallon->qty){
            return false;
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

        $customer_gallon->qty -= $returned_gallons;
        if($customer_gallon->qty > 0){
            if(!$customer_gallon->save()){
                return false;
            }
        }
        else{
            if(!$customer_gallon->delete()){
                return false;
            }
        }

        

        //create nomor_faktur
        if($this->status=="Draft"){
            $re_header_invoice = (new ReHeaderInvoice())->doMake($this);
            if($this->empty_gallon_quantity>0){
                $empty = (new OrderCustomerReturnInvoice())->doMake($this, $re_header_invoice->id, "empty");
            }
            if($this->filled_gallon_quantity>0){
                $filled = (new OrderCustomerReturnInvoice())->doMake($this, $re_header_invoice->id, "filled");
            }
        }else if($this->status=="Batal"){
            //update faktur_detail
            $invoice_details = OrderCustomerReturnInvoice::where('order_customer_return_id',$this->id)->get();
            foreach ($invoice_details as $invoice_detail) {
                $invoice_detail->subtotal = $invoice_detail->quantity * Price::find($invoice_detail->price_id)->price;
                $invoice_detail->save();
            }
            $invoice_details[0]->reHeaderInvoice->payment_date = Carbon::now()->format('Y-n-d H:i:s');
            $invoice_details[0]->reHeaderInvoice->save();
        }

        $this->status = 'Selesai';

        return $this->save();
    }

    public function doCancel(){
        $customer_gallon = CustomerGallon::with('customer')
            ->where([
                ['customer_id', $this->customer_id],
                ['type', 'rent']
            ])
            ->first();

        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);

        $returned_gallons = $this->empty_gallon_quantity + $this->filled_gallon_quantity;

        if(!$customer_gallon){
            $customer_gallon = new CustomerGallon();
            $customer_gallon->customer_id = $this->customer_id;
            $customer_gallon->qty = $returned_gallons;
            $customer_gallon->type = "rent";
            if(!$customer_gallon->save()){
                return false;
            }
        }
        else{
            $customer_gallon->qty += $returned_gallons;

            if(!$customer_gallon->save()){
                return false;
            }
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

        $this->status = 'Batal';

        


        return $this->save();
    }
}
