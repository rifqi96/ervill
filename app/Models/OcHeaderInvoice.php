<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\SoftDeletes;

class OcHeaderInvoice extends Model
{
    use SoftDeletes;
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
    public function orderCustomers(){
        return $this->hasMany('App\Models\OrderCustomer');
    }
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
    }
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }

    public function doMake($data, $author_id){

    	$id = OcHeaderInvoice::withTrashed()->orderBy('id','desc')->pluck('id')->first();
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
            $this->payment_date = Carbon::now()->format('Y-m-d H:i:s');
    	}

    	if($data->is_free){
    		$this->is_free = "true";
            $this->payment_date = Carbon::now()->format('Y-m-d H:i:s');
    	}else{
    		$this->is_free = "false";
    	}

    	$this->user_id = $author_id;
        // Get customer data
        if($data->new_customer){
            if( !($customer = (new Customer())->doMake($data)) ){
                return false;
            }

            $data->customer_id = $customer->id;
            $data['customer_id'] = $customer->id;
        }
        else{
            if( !($customer = Customer::find($data->customer_id)) ){
                return false;
            }
        }

        if($data->pay_qty > 0){
            if($data->pay_qty > $customer->rent_qty){
                $validator = Validator::make([], []); // Empty data and rules fields
                $validator->errors()->add('id', 'Jumlah galon pinjam tidak cukup');
                throw new ValidationException($validator);
            }
        }
        if($data->refill_qty && $data->refill_qty > 0 && $data->new_customer){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('id', 'Jumlah galon pada customer tidak cukup utk isi ulang');
            throw new ValidationException($validator);
        }

        if($data->additional_price){
            $this->additional_price = $data->additional_price;
        }
        if($data->description){
            $this->description = $data->description;
        }
        $this->customer_id = $data->customer_id;
        $this->delivery_at = $data->delivery_at;
        $this->status = "Draft";

    	if(!$this->save()){
    	    return false;
        }

        $invoice_no = $this->id;

        if(!$this->doMakeDetails($data, $customer, $invoice_no)){
            return false;
        }

    	return $this;
    }

    public function doUpdate($data){
        $old_data = OcHeaderInvoice::with(['orderCustomers', 'customer'])->find($this->id);

        if($data->pay_qty > 0){
            if($data->pay_qty > $this->customer->rent_qty){
                $validator = Validator::make([], []); // Empty data and rules fields
                $validator->errors()->add('id', 'Jumlah galon pinjam tidak cukup');
                throw new ValidationException($validator);
            }
        }

        if($data->is_piutang){
            $this->payment_status = "piutang";
            $this->payment_date = null;
        }else{
            $this->payment_status = "cash";
            $this->payment_date = Carbon::now()->format('Y-m-d H:i:s');
        }

        if($data->is_free){
            $this->is_free = "true";
            $this->payment_date = Carbon::now()->format('Y-m-d H:i:s');
        }else{
            $this->is_free = "false";
        }

        if(!$this->shipment_id || $this->status != 'Draft'){
            $this->delivery_at = $data->delivery_at;
        }

        if($data->additional_price){
            $this->additional_price = $data->additional_price;
        }
        else{
            $this->additional_price = null;
        }

        if($data->oc_description){
            $this->description = $data->description;
        }
        else{
            $this->description = null;
        }

        if(!$this->save() || !$this->doDeleteDetails() || !$this->doMakeDetails($data, $this->customer, $this->id)){
            return false;
        }

        $new_data = OcHeaderInvoice::find($this->id);
        if(!$new_data->doAddToEditHistory($old_data, $data)){
            return false;
        }

        return true;
    }

    public function doDeleteDetails($remove_oc = true){
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);
        $non_erv_gallon = Inventory::find(6);
        $sold_gallon = Inventory::find(7);

        // Restore Inventory
        $refill_qty = $rent_qty = $purchase_qty = $non_erv_qty = $pay_qty = 0;
        foreach($this->orderCustomers as $orderCustomer){
            if($orderCustomer->price_id == 8 || $orderCustomer->price_id == 1){
                $refill_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 9 || $orderCustomer->price_id == 2){
                $rent_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 11 || $orderCustomer->price_id == 4){
                $purchase_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 10 || $orderCustomer->price_id == 3){
                $non_erv_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 12 || $orderCustomer->price_id == 5){
                $pay_qty += $orderCustomer->quantity;
            }

            // Delete details
            if($remove_oc){
                if(!$orderCustomer->delete()){
                    return false;
                }
            }
        }

        $this->customer->rent_qty -= $rent_qty;
        $this->customer->rent_qty += $pay_qty;
        $this->customer->purchase_qty -= $purchase_qty + $pay_qty;
        $this->customer->non_erv_qty -= $non_erv_qty;

        if(!$this->customer->save() || !$empty_gallon->subtract($refill_qty) || !$filled_gallon->add($refill_qty + $rent_qty + $purchase_qty + $non_erv_qty) || !$outgoing_gallon->add($pay_qty) || !$outgoing_gallon->subtract($rent_qty) || !$sold_gallon->subtract($purchase_qty + $pay_qty) || !$non_erv_gallon->subtract($non_erv_qty)){
            return false;
        }

        return true;
    }

    public function doMakeDetails($data, $customer, $invoice_no){
        // Create Details
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);
        $non_erv_gallon = Inventory::find(6);
        $sold_gallon = Inventory::find(7);
        $details = collect();
        $customer_gallons = new \stdClass();
        $additional_price = $data->additional_price ? $data->additional_price:null;

        // Get Price ID

        if($data->refill_qty && $data->refill_qty > 0){
            $collect_data = new \stdClass();
            $collect_data->qty = $data->refill_qty;
            if($customer->type == "agent"){
                $collect_data->price_id = 8;
            }
            else{
                $collect_data->price_id = 1;
            }
            $details->push($collect_data);
        }
        if($data->rent_qty && $data->rent_qty > 0){
            $collect_data = new \stdClass();
            $collect_data->qty = $data->rent_qty;
            if($customer->type == "agent"){
                $collect_data->price_id = 9;
            }
            else{
                $collect_data->price_id = 2;
            }
            $customer_gallons->rent_qty = $data->rent_qty;
            $details->push($collect_data);
        }
        if($data->purchase_qty && $data->purchase_qty > 0){
            $collect_data = new \stdClass();
            $collect_data->qty = $data->purchase_qty;
            if($customer->type == "agent"){
                $collect_data->price_id = 11;
            }
            else{
                $collect_data->price_id = 4;
            }
            $customer_gallons->purchase_qty = $data->purchase_qty;
            $details->push($collect_data);
        }
        if($data->non_erv_qty && $data->non_erv_qty > 0){
            $collect_data = new \stdClass();
            $collect_data->qty = $data->non_erv_qty;
            if($customer->type == "agent"){
                $collect_data->price_id = 10;
            }
            else{
                $collect_data->price_id = 3;
            }
            $customer_gallons->non_erv_qty = $data->non_erv_qty;
            $details->push($collect_data);
        }
        if($data->pay_qty && $data->pay_qty > 0){
            $collect_data = new \stdClass();
            $collect_data->qty = $data->pay_qty;
            if($customer->type == "agent"){
                $collect_data->price_id = 12;
            }
            else{
                $collect_data->price_id = 5;
            }
            $customer_gallons->pay_qty = $data->pay_qty;
            $details->push($collect_data);
        }

        foreach($details as $detail){
            if(!(new OrderCustomer())->doMake($detail, $invoice_no, $additional_price)){
                return false;
            }
        }

        if(!$data->refill_qty){
            $data->refill_qty = 0;
            $data['refill_qty'] = 0;
        }

        if(!$customer->doUpdateGallons($customer_gallons) || !$filled_gallon->subtract($data->refill_qty + $data->rent_qty + $data->purchase_qty + $data->non_erv_qty) || !$empty_gallon->add($data->refill_qty?$data->refill_qty:0) || !$outgoing_gallon->add($data->rent_qty) || !$outgoing_gallon->subtract($data->pay_qty) || !$sold_gallon->add($data->purchase_qty + $data->pay_qty) || !$non_erv_gallon->add($data->non_erv_qty)){
            return false;
        }

        return true;
    }

    public function doPay(){
        if($this->payment_status == "cash"){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('id', 'Gagal update, status faktur telah lunas');
            throw new ValidationException($validator);
        }

        $this->payment_date = Carbon::now()->format('Y-m-d H:i:s');
        $this->payment_status = 'cash';

        return $this->save();
    }

    public function setInvoiceAttributes(){
        $filled_gallon = 0;
        $empty_gallon = 0;
        $non_erv_gallon = 0;

        $refill_qty = 0;
        $rent_qty = 0;
        $purchase_qty = 0;
        $non_erv_qty = 0;
        $pay_qty = 0;

        $total = 0;

        foreach($this->orderCustomers as $orderCustomer){
            // Refill
            if($orderCustomer->priceMaster->id == 1 || $orderCustomer->priceMaster->id == 8){
                $filled_gallon += $orderCustomer->quantity;
                $empty_gallon += $orderCustomer->quantity;

                $refill_qty += $orderCustomer->quantity;
            }

            // Non Ervill
            else if($orderCustomer->priceMaster->id == 3 || $orderCustomer->priceMaster->id == 10){
                $filled_gallon += $orderCustomer->quantity;
                $non_erv_gallon += $orderCustomer->quantity;

                $non_erv_qty += $orderCustomer->quantity;
            }

            // Others
            else{
                if($orderCustomer->priceMaster->id == 2 || $orderCustomer->priceMaster->id == 9){
                    $rent_qty += $orderCustomer->quantity;
                    $filled_gallon += $orderCustomer->quantity;
                }
                else if($orderCustomer->priceMaster->id == 4 || $orderCustomer->priceMaster->id == 11){
                    $purchase_qty += $orderCustomer->quantity;
                    $filled_gallon += $orderCustomer->quantity;
                }
                else{
                    $pay_qty += $orderCustomer->quantity;
                }
            }

            // Total
            if($this->is_free != 'true'){
                $total += $orderCustomer->subtotal;
            }
        }

        $this->filled_gallon = $filled_gallon;
        $this->empty_gallon = $empty_gallon;
        $this->non_erv_gallon = $non_erv_gallon;
        $this->refill_qty = $refill_qty;
        $this->rent_qty = $rent_qty;
        $this->purchase_qty = $purchase_qty;
        $this->non_erv_qty = $non_erv_qty;
        $this->pay_qty = $pay_qty;
        $this->total = $total;
        $this->invoice_code = "oc";
        $this->type = "sales";

        $this->payment_status_txt = "LUNAS";

        if($this->payment_status == "piutang"){
            $this->payment_status_txt = "PIUTANG";
        }
        else if($this->is_free == "true"){
            $this->payment_status_txt = "FREE atau SAMPLE";
        }

        if($this->deleted_at){
            $this->status = "Dihapus";
        }
    }

    //////////api/////////
    public function doStartShipment(){
        $this->status = 'Proses';
        return $this->save();
    }

    public function doDropGallon(){
        $this->status = 'Selesai';
        return $this->save();
    }

    public function doCancelTransaction(){
          
        if(!$this->doDeleteDetails(false)){
            return false;
        }
        $this->status = 'Batal';  
        return $this->save();
    }
    
    public function doRemoveShipment(){
        $this->shipment_id = null;
        return $this->save();
    }

    public function doAddToEditHistory($old_data, $data){
        //set old values
        $old_value = '';
        $refill_qty = $rent_qty = $purchase_qty = $non_erv_qty = $pay_qty = 0;
        foreach($old_data->orderCustomers as $orderCustomer){
            if($orderCustomer->price_id == 8 || $orderCustomer->price_id == 1){
                $refill_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 9 || $orderCustomer->price_id == 2){
                $rent_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 11 || $orderCustomer->price_id == 4){
                $purchase_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 10 || $orderCustomer->price_id == 3){
                $non_erv_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 12 || $orderCustomer->price_id == 5){
                $pay_qty += $orderCustomer->quantity;
            }
        }
        $old_value .= $refill_qty . ";";
        $old_value .= $rent_qty . ";";
        $old_value .= $purchase_qty . ";";
        $old_value .= $non_erv_qty . ";";
        $old_value .= $pay_qty . ";";
        $old_value .= $old_data->payment_status . ";";
        $old_value .= $old_data->is_free == "true" ? "gratis/sample;":"penjualan;";
        $old_value .= Carbon::parse($old_data->delivery_at)->format('d-m-Y');

        //set new values
        $new_value = '';
        $refill_qty = $rent_qty = $purchase_qty = $non_erv_qty = $pay_qty = 0;
        foreach($this->orderCustomers as $orderCustomer){
            if($orderCustomer->price_id == 8 || $orderCustomer->price_id == 1){
                $refill_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 9 || $orderCustomer->price_id == 2){
                $rent_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 11 || $orderCustomer->price_id == 4){
                $purchase_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 10 || $orderCustomer->price_id == 3){
                $non_erv_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 12 || $orderCustomer->price_id == 5){
                $pay_qty += $orderCustomer->quantity;
            }
        }
        $new_value .= $refill_qty . ";";
        $new_value .= $rent_qty . ";";
        $new_value .= $purchase_qty . ";";
        $new_value .= $non_erv_qty . ";";
        $new_value .= $pay_qty . ";";
        $new_value .= $this->payment_status . ";";
        $new_value .= $this->is_free == "true" ? "gratis/sample;":"penjualan;";
        $new_value .= Carbon::parse($data->delivery_at)->format('d-m-Y');

        $edit_data = array(
            'module_name' => 'Order Customer',
            'data_id' => $this->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => auth()->id()
        );

        return EditHistory::create($edit_data);
    }

    public function doDelete($data, $user_id){
        $data = array(
            'module_name' => 'Order Customer',
            'description' => $data->description,
            'data_id' => $this->id,
            'user_id' => $user_id
        );

        if(!$this->doDeleteDetails(false) || !$this->delete() || !DeleteHistory::create($data)) {
            return false;
        }

        return true;
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }

    public function doRestore(){
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);
        $non_erv_gallon = Inventory::find(6);
        $sold_gallon = Inventory::find(7);

        if(!$this->restore()){
            return false;
        }

        // Restore Inventory
        $refill_qty = $rent_qty = $purchase_qty = $non_erv_qty = $pay_qty = 0;
        foreach($this->orderCustomers as $orderCustomer){
            if($orderCustomer->price_id == 8 || $orderCustomer->price_id == 1){
                $refill_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 9 || $orderCustomer->price_id == 2){
                $rent_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 11 || $orderCustomer->price_id == 4){
                $purchase_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 10 || $orderCustomer->price_id == 3){
                $non_erv_qty += $orderCustomer->quantity;
            }
            if($orderCustomer->price_id == 12 || $orderCustomer->price_id == 5){
                $pay_qty += $orderCustomer->quantity;
            }
        }

        $this->customer->rent_qty += $rent_qty;
        $this->customer->rent_qty -= $pay_qty;
        $this->customer->purchase_qty += $purchase_qty + $pay_qty;
        $this->customer->non_erv_qty += $non_erv_qty;

        if(!$this->customer->save() || !$empty_gallon->add($refill_qty) || !$filled_gallon->subtract($refill_qty + $rent_qty + $purchase_qty + $non_erv_qty) || !$outgoing_gallon->subtract($pay_qty) || !$outgoing_gallon->add($rent_qty) || !$sold_gallon->add($purchase_qty + $pay_qty) || !$non_erv_gallon->add($non_erv_qty)){
            return false;
        }

        return true;
    }
}
