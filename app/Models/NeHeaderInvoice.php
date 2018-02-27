<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class NeHeaderInvoice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    //for non-auto-increment PK
    protected $primaryKey = 'id';
    public $incrementing = false;

    public function orderCustomerNonErvills(){
        return $this->hasMany('App\Models\OrderCustomerNonErvill');
    }
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function customerNonErvill(){
        return $this->belongsTo('App\Models\CustomerNonErvill');
    }

    public function doMake($data, $author_id){

        $id = NeHeaderInvoice::withTrashed()->orderBy('id','desc')->pluck('id')->first();
        if($id){
            $id = (string)((int)substr($id,2)+1);
            while( strlen($id) < 7 ){
                $id = '0'.$id;
            }
            $this->id = 'NE'.$id;
        }else{
            $this->id = 'NE0000001';
        }
        
        if($data->is_piutang){
            $this->payment_status = "piutang";
        }else{
            $this->payment_status = "cash";
            $this->payment_date = Carbon::now()->format('Y-m-d H:i:s');
        }
        

        $this->user_id = $author_id;
        // Get customer data
        if($data->new_customer){
            if( !($customer = (new CustomerNonErvill())->doMake($data)) ){
                return false;
            }

            $data->customer_id = $customer->id;
            $data['customer_id'] = $customer->id;
        }
        else{
            if( !($customer = CustomerNonErvill::find($data->customer_id)) ){
                return false;
            }
        }

        // if($data->pay_qty > 0){
        //     if($data->pay_qty > $customer->rent_qty){
        //         $validator = Validator::make([], []); // Empty data and rules fields
        //         $validator->errors()->add('id', 'Jumlah galon pinjam tidak cukup');
        //         throw new ValidationException($validator);
        //     }
        // }
        // if($data->refill_qty && $data->refill_qty > 0 && $data->new_customer){
        //     $validator = Validator::make([], []); // Empty data and rules fields
        //     $validator->errors()->add('id', 'Jumlah galon pada customer tidak cukup utk isi ulang');
        //     throw new ValidationException($validator);
        // }

        if($data->additional_price){
            $this->additional_price = $data->additional_price;
        }
        if($data->description){
            $this->description = $data->description;
        }
        $this->customer_non_ervill_id = $data->customer_id;
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


    public function doMakeDetails($data, $customer, $invoice_no){
        // Create Details       
        $non_erv_gallon = Inventory::find(6);
        $aqua_gallon = Inventory::find(8);
        $non_aqua_gallon = Inventory::find(9);
        $details = collect();
        $customer_gallons = new \stdClass();
        $additional_price = $data->additional_price ? $data->additional_price:null;

        // Get Price ID

        if($data->aqua_qty && $data->aqua_qty > 0){
            $collect_data = new \stdClass();
            $collect_data->qty = $data->aqua_qty;            
            $collect_data->price_id = 15;

            $customer_gallons->aqua_qty = $data->aqua_qty;
            $details->push($collect_data);
        }
        if($data->non_aqua_qty && $data->non_aqua_qty > 0){
            $collect_data = new \stdClass();
            $collect_data->qty = $data->non_aqua_qty;           
            $collect_data->price_id = 16;
            
            $customer_gallons->non_aqua_qty = $data->non_aqua_qty;
            $details->push($collect_data);
        }
        

        foreach($details as $detail){
            if(!(new OrderCustomerNonErvill())->doMake($detail, $invoice_no, $additional_price)){
                return false;
            }
        }
        

        if(!$customer->doUpdateGallons($customer_gallons) || !$non_erv_gallon->subtract($data->aqua_qty+$data->non_aqua_qty) || !$aqua_gallon->add($data->aqua_qty) || !$non_aqua_gallon->add($data->non_aqua_qty) ){
            return false;
        }

        return true;
    }

    public function doDeleteDetails($remove_oc = true){
        $non_erv_gallon = Inventory::find(6);
        $aqua_gallon = Inventory::find(8);
        $non_aqua_gallon = Inventory::find(9);

        // Restore Inventory
        $aqua_qty = $non_aqua_qty = 0;
        foreach($this->orderCustomerNonErvills as $orderCustomerNonErvill){
            if($orderCustomerNonErvill->price_id == 15){
                $aqua_qty += $orderCustomerNonErvill->quantity;
            }
            if($orderCustomerNonErvill->price_id == 16){
                $non_aqua_qty += $orderCustomerNonErvill->quantity;
            }
            

            // Delete details
            if($remove_oc){
                if(!$orderCustomerNonErvill->delete()){
                    return false;
                }
            }
        }

        $this->customerNonErvill->aqua_qty -= $aqua_qty;
        $this->customerNonErvill->non_aqua_qty -= $non_aqua_qty;
      

        if(!$this->customerNonErvill->save() || !$non_erv_gallon->add($aqua_qty+$non_aqua_qty) || !$aqua_gallon->subtract($aqua_qty) || !$non_aqua_gallon->subtract($non_aqua_qty) ){
            return false;
        }

        return true;
    }

    public function doUpdate($data){
        $old_data = NeHeaderInvoice::with(['orderCustomerNonErvills', 'customerNonErvill'])->find($this->id);

        // if($data->aqua_qty > 0){
        //     if($data->pay_qty > $this->customer->rent_qty){
        //         $validator = Validator::make([], []); // Empty data and rules fields
        //         $validator->errors()->add('id', 'Jumlah galon pinjam tidak cukup');
        //         throw new ValidationException($validator);
        //     }
        // }

        if($data->is_piutang){
            $this->payment_status = "piutang";
            $this->payment_date = null;
        }else{
            $this->payment_status = "cash";
            $this->payment_date = Carbon::now()->format('Y-m-d H:i:s');
        }

        

        //if( $this->status != 'Draft'){
            $this->delivery_at = $data->delivery_at;
        //}

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

        if(!$this->save() || !$this->doDeleteDetails() || !$this->doMakeDetails($data, $this->customerNonErvill, $this->id)){
            return false;
        }

        $new_data = NeHeaderInvoice::find($this->id);
        if(!$new_data->doAddToEditHistory($old_data, $data)){
            return false;
        }

        return true;
    }

    public function doDelete($data, $user_id){
        $data = array(
            'module_name' => 'Order Customer Pihak Ketiga',
            'description' => $data->description,
            'data_id' => $this->id,
            'user_id' => $user_id
        );

        if(!$this->doDeleteDetails(false) || !$this->delete() || !DeleteHistory::create($data)) {
            return false;
        }

        return true;
    }

    public function doConfirm($data){

        // $empty_gallon = Inventory::find(1);

        // //recalculate inventory
        // $empty_gallon->quantity += ($this->order->quantity);

        //update order gallon and order data   
        $this->accepted_at = Carbon::now();     
        $this->status = "Selesai";  

        // if(!$this->order->save() || !$empty_gallon->save() ){
        //     return false;
        // }

        return ($this->save()); 
    }

    public function doCancel($data){

        // $empty_gallon = Inventory::find(1);

        // //recalculate inventory
        // $empty_gallon->quantity += ($this->order->quantity);

        //update order gallon and order data   
        $this->accepted_at = null;     
        $this->status = "Draft";  

        // if(!$this->order->save() || !$empty_gallon->save() ){
        //     return false;
        // }

        return ($this->save()); 
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }

    public function doRestore(){
        $non_erv_gallon = Inventory::find(6);
        $aqua_gallon = Inventory::find(8);
        $non_aqua_gallon = Inventory::find(9);

        if(!$this->restore()){
            return false;
        }

        // Restore Inventory
        $aqua_qty = $non_aqua_qty = 0;
        foreach($this->orderCustomerNonErvills as $orderCustomerNonErvill){
            if($orderCustomerNonErvill->price_id == 15){
                $aqua_qty += $orderCustomerNonErvill->quantity;
            }
            if($orderCustomerNonErvill->price_id == 16){
                $non_aqua_qty += $orderCustomerNonErvill->quantity;
            }
        }

        $this->customerNonErvill->aqua_qty += $aqua_qty;
        $this->customerNonErvill->non_aqua_qty += $non_aqua_qty;
       

        if(!$this->customerNonErvill->save() || !$non_erv_gallon->subtract($aqua_qty+$non_aqua_qty) || !$aqua_gallon->add($aqua_qty) || !$non_aqua_gallon->add($non_aqua_qty) ){
            return false;
        }

        return true;
    }

    public function doAddToEditHistory($old_data, $data){
        //set old values
        $old_value = '';
        $aqua_qty = $non_aqua_qty = 0;
        foreach($old_data->orderCustomerNonErvills as $orderCustomerNonErvill){
            if($orderCustomerNonErvill->price_id == 15){
                $aqua_qty += $orderCustomerNonErvill->quantity;
            }
            if($orderCustomerNonErvill->price_id == 16){
                $non_aqua_qty += $orderCustomerNonErvill->quantity;
            }
            
        }
        $old_value .= $aqua_qty . ";";
        $old_value .= $non_aqua_qty . ";";        
        $old_value .= $old_data->payment_status . ";";       
        $old_value .= Carbon::parse($old_data->delivery_at)->format('d-m-Y');

        //set new values
        $new_value = '';
        $aqua_qty = $non_aqua_qty = 0;
        foreach($this->orderCustomerNonErvills as $orderCustomerNonErvill){
            if($orderCustomerNonErvill->price_id == 15){
                $aqua_qty += $orderCustomerNonErvill->quantity;
            }
            if($orderCustomerNonErvill->price_id == 16){
                $non_aqua_qty += $orderCustomerNonErvill->quantity;
            }
        }
        $new_value .= $aqua_qty . ";";
        $new_value .= $non_aqua_qty . ";";       
        $new_value .= $this->payment_status . ";";        
        $new_value .= Carbon::parse($data->delivery_at)->format('d-m-Y');

        $edit_data = array(
            'module_name' => 'Order Customer Pihak Ketiga',
            'data_id' => $this->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => auth()->id()
        );

        return EditHistory::create($edit_data);
    }


    public function setInvoiceAttributes(){
        $aqua_gallon = 0;
        $non_aqua_gallon = 0;

        $total = 0;

        foreach($this->orderCustomerNonErvills as $orderCustomerNonErvill){
            // Aqua
            if($orderCustomerNonErvill->priceMaster->id == 15 ){
                $aqua_gallon += $orderCustomerNonErvill->quantity;            
            }

            // Non Aqua
            else if($orderCustomerNonErvill->priceMaster->id == 16 ){
                $non_aqua_gallon += $orderCustomerNonErvill->quantity;               
            }
           

            // Total          
            $total += $orderCustomerNonErvill->subtotal;
            
        }

        $this->aqua_gallon = $aqua_gallon;
        $this->non_aqua_gallon = $non_aqua_gallon;       
        $this->total = $total;

        $this->payment_status_txt = "LUNAS";

        if($this->payment_status == "piutang"){
            $this->payment_status_txt = "PIUTANG";
        }        

        if($this->deleted_at){
            $this->status = "Dihapus";
        }
    }
}
