<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory;
use App\Models\EditHistory;
use App\Models\DeleteHistory;
use Illuminate\Support\Collection;
use PhpParser\ErrorHandler\Collecting;
use App\Models\CustomerGallon;
use Validator;
use Illuminate\Validation\ValidationException;

class OrderCustomer extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment');
    }
    public function orderCustomerInvoices()
    {
        return $this->hasMany('App\Models\OrderCustomerInvoice');
    }

    public function getRecentOrders(){
        return $this->with([
            'shipment' => function($query){
                $query->with(['user']);
            },
            'customer',
            'order' => function($query){
                $query->with(['user', 'issues']);
            }
            ])
//            ->whereHas('order', function ($query){
//                $query->whereDate('created_at', '=', Carbon::today()->toDateString());
//            })
            ->has('order')
            ->whereDate('delivery_at', '=', Carbon::today()->toDateString())
            ->get();
    }

    public function doMake($gallon_data, $author_id)
    {

        /////////////validation start////////////////

        if($gallon_data->change_nomor_struk){

            //check whether invalid nomor_struk
            $oc_struk = OrderCustomer::whereHas('orderCustomerInvoices',function($query) use($gallon_data){
                $query->where('oc_header_invoice_id',$gallon_data->nomor_struk);
            })
            ->where([
                ['customer_id',$gallon_data->customer_id]
            ])->get();

            if(count($oc_struk)==0){
                return false;
            }
        }           
      

        //////////////////////validation finish////////////////////
        $order_data = (new Order)->doMakeOrderCustomer($gallon_data, $author_id);
        
        if($gallon_data->new_customer){        
            $customer = (new Customer())->doMake($gallon_data);         
            $customerGallon = (new CustomerGallon())->doMake($gallon_data,$customer->id);
        }else{
            $customer = Customer::find($gallon_data->customer_id);
        }
        

        $this->order_id = $order_data->id;
        $this->customer_id = $customer->id;
        if($gallon_data->new_customer){
            $this->empty_gallon_quantity = 0;
            $this->purchase_type = $gallon_data->purchase_type;
            $this->is_new = 'true';
        }else{
            $this->empty_gallon_quantity = $gallon_data->quantity;
            $this->is_new = 'false';
        }
        if($gallon_data->add_gallon){
            $this->additional_quantity = $gallon_data->add_gallon_quantity;
            $this->purchase_type = $gallon_data->add_gallon_purchase_type;
        }
        $this->delivery_at = $gallon_data->delivery_at;
        $this->status = "Draft";
        
        $this->save();

        if($gallon_data->change_nomor_struk){            
            $orderCustomerInvoice = (new OrderCustomerInvoice())->doMake($this, $gallon_data->nomor_struk);
            //refill and add gallon
            if($this->purchase_type && $this->is_new=="false" && $this->order->quantity!=0){
                $orderCustomerInvoice = (new OrderCustomerInvoice())->doMake($this, $gallon_data->nomor_struk, true);
            }
        }else{
            $oc_header_invoice = (new OcHeaderInvoice())->doMake($gallon_data);
            $orderCustomerInvoice = (new OrderCustomerInvoice())->doMake($this, $oc_header_invoice->id);
            //refill and add gallon
            if($this->purchase_type && $this->is_new=="false" && $this->order->quantity!=0){
                $orderCustomerInvoice = (new OrderCustomerInvoice())->doMake($this, $oc_header_invoice->id, true);
            }
        }

        return true;
    }

    public function doUpdate($data)
    {
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);
        $non_ervill_gallon = Inventory::find(6);
        $sold_gallon = Inventory::find(7);

        $filled_stock = (integer)$filled_gallon->quantity+$this->order->quantity;

        if($filled_stock < $data->quantity){
            return false;
        }

        // //check if empty_gallon_qty exceeds gallon_qty
        // if($data->empty_gallon_quantity > $data->quantity){
        //     return false;
        // }

        $old_data = $this->toArray();
        $old_data['oc_header_invoice_id'] = $this->orderCustomerInvoices[0]['oc_header_invoice_id'];
       
        
        $filled_gallon->quantity = ($filled_gallon->quantity + $this->order->quantity) - $data->quantity;

        //edit nomor_struk

        //check whether invalid nomor_struk
        $oc_struk = OrderCustomer::whereHas('orderCustomerInvoices',function($query) use($data){
            $query->where('oc_header_invoice_id',$data->nomor_struk);
        })
        ->where([
            ['customer_id',$data->customer_id]
        ])->get();

        if(count($oc_struk)==0){       
            //nomor_struk exception  
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('nomor_struk', 'Input nomor faktur salah, mohon diperiksa kembali');
            throw new ValidationException($validator);
            
            //return false;
        }
       
        ///////////////////validation finish//////////////////////

        //$this->nomor_struk = $data->nomor_struk;

       //change no struk
       if($data->nomor_struk!=$this->orderCustomerInvoices[0]->oc_header_invoice_id){
            foreach($this->orderCustomerInvoices as $orderCustomerInvoice){
                $orderCustomerInvoice->oc_header_invoice_id = $data->nomor_struk;
                $orderCustomerInvoice->save();
            }
       }

        //change customer
        if($this->customer_id != $data->customer_id){
            //remove previoius additional gallon
            if($this->purchase_type){
                if($this->purchase_type=="rent"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            if($this->is_new=="true"){
                                $outgoing_gallon->quantity -= $this->order->quantity;
                                $customerGallon->qty -= $this->order->quantity;
                            }else{
                                $outgoing_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty -= $this->additional_quantity;
                            }

                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }else if($this->purchase_type=="purchase"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){
                            if($this->is_new=="true"){
                                $customerGallon->qty -= $this->order->quantity;
                            }else{
                                $customerGallon->qty -= $this->additional_quantity;
                            }

                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }else if($this->purchase_type=="non_ervill"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){
                            if($this->is_new=="true"){
                                $non_ervill_gallon->quantity -= $this->order->quantity;
                                $customerGallon->qty -= $this->order->quantity;
                            }else{
                                $non_ervill_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty -= $this->additional_quantity;
                            }

                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }

            }

            //update new chosen customer
            $empty_gallon->quantity = ($empty_gallon->quantity - $this->empty_gallon_quantity) + $data->quantity;
            if($data->add_gallon){
                $chosenCustomerGallons = CustomerGallon::where('customer_id',$data->customer_id);
                //rent
                if($data->add_gallon_purchase_type=="rent"){
                    $outgoing_gallon->quantity += $data->add_gallon_quantity;

                    $is_update=false;
                    foreach($chosenCustomerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$data->customer_id);
                    }
                }
                //purchase
                else if($data->add_gallon_purchase_type=="purchase"){

                    $is_update=false;
                    foreach($chosenCustomerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$data->customer_id);
                    }
                }
                //non_ervill
                else if($data->add_gallon_purchase_type=="non_ervill"){
                    $non_ervill_gallon->quantity += $data->add_gallon_quantity;
                    $is_update=false;
                    foreach($chosenCustomerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$data->customer_id);
                    }
                }


                //update additional quantity and purchase type, filled gallon
                $filled_gallon->quantity -= ($data->add_gallon_quantity - $this->additional_quantity);
                $this->purchase_type = $data->add_gallon_purchase_type;
                $this->additional_quantity = $data->add_gallon_quantity;
            }else{
                //no additional gallon
                $filled_gallon->quantity += $this->additional_quantity;
                $this->purchase_type = null;
                $this->additional_quantity = 0;
            }

            $this->is_new="false";
            $this->empty_gallon_quantity = $data->quantity;
            $this->customer_id = $data->customer_id;
        }

        //new customer
        else if($this->is_new=='true'){
            if($this->purchase_type=="rent"){
                //rent to rent
                if($data->purchase_type=="rent"){
                    $outgoing_gallon->quantity += ($data->quantity - $this->order->quantity);
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $customerGallon->qty += ($data->quantity - $this->order->quantity);
                            $customerGallon->save();
                            break;
                        }
                    }
                }
                //rent to purchase
                else if($data->purchase_type=="purchase"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $outgoing_gallon->quantity -= $customerGallon->qty;   
                            $customerGallon->type="purchase";
                            $customerGallon->qty+= ($data->quantity - $this->order->quantity);
                            $customerGallon->save();
                            break;                    
                        }
                    }
                }    
                //rent to non_ervill
                else if($data->purchase_type=="non_ervill"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $outgoing_gallon->quantity -= $customerGallon->qty;         
                            $customerGallon->type="non_ervill";
                            $customerGallon->qty+= ($data->quantity - $this->order->quantity);
                            $customerGallon->save();   
                            $non_ervill_gallon->quantity += $customerGallon->qty;
                            break;            
                        }
                    }
                }            
            }else if($this->purchase_type=="purchase"){
                //purchase to rent
                if($data->purchase_type=="rent"){                    
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){
                            $customerGallon->type="rent";
                            $customerGallon->qty += ($data->quantity - $this->order->quantity);
                            $customerGallon->save();
                            $outgoing_gallon->quantity += $customerGallon->qty;
                            break;
                        }
                    }
                }
                //purchase to purchase
                else if($data->purchase_type=="purchase"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){            
                            $customerGallon->qty+= ($data->quantity - $this->order->quantity);
                            $customerGallon->save();
                            break;                    
                        }
                    }
                }    
                //purchase to non_ervill
                else if($data->purchase_type=="non_ervill"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){                                    
                            $customerGallon->type="non_ervill";
                            $customerGallon->qty+= ($data->quantity - $this->order->quantity);
                            $customerGallon->save();   
                            $non_ervill_gallon->quantity += $customerGallon->qty;
                            break;            
                        }
                    }
                }            
            }else if($this->purchase_type=="non_ervill"){
                //non_ervill to rent
                if($data->purchase_type=="rent"){                    
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){
                            $non_ervill_gallon->quantity -= $customerGallon->qty;
                            $customerGallon->type="rent";
                            $customerGallon->qty += ($data->quantity - $this->order->quantity);
                            $customerGallon->save();
                            $outgoing_gallon->quantity += $customerGallon->qty;
                            break;
                        }
                    }
                }
                //non_ervill to purchase
                else if($data->purchase_type=="purchase"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){  
                            $non_ervill_gallon->quantity -= $customerGallon->qty;  
                            $customerGallon->type="purchase";        
                            $customerGallon->qty+= ($data->quantity - $this->order->quantity);
                            $customerGallon->save();
                            break;                    
                        }
                    }
                }    
                //non_ervill to non_ervill
                else if($data->purchase_type=="non_ervill"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){                                   
                            $customerGallon->qty+= ($data->quantity - $this->order->quantity);
                            $customerGallon->save();   
                            $non_ervill_gallon->quantity += ($data->quantity - $this->order->quantity);
                            break;            
                        }
                    }
                }            
            }
            //update purchase type
            $this->purchase_type = $data->purchase_type;

        //existing customer
        }else{
            $empty_gallon->quantity = ($empty_gallon->quantity - $this->empty_gallon_quantity) + $data->quantity;

            //remove previoius additional gallon
            if($this->purchase_type){         
                if($this->purchase_type=="rent"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $outgoing_gallon->quantity -= $this->additional_quantity;
                            $customerGallon->qty -= $this->additional_quantity;         
                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }else if($this->purchase_type=="purchase"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){
                            $sold_gallon->quantity -= $this->additional_quantity;
                            $customerGallon->qty -= $this->additional_quantity;         
                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }else if($this->purchase_type=="non_ervill"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){   
                            $non_ervill_gallon->quantity -= $this->additional_quantity;    
                            $customerGallon->qty -= $this->additional_quantity;         
                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }
                
            }

            //add gallon
            if($data->add_gallon){               
                //rent
                if($data->add_gallon_purchase_type=="rent"){
                    $outgoing_gallon->quantity += $data->add_gallon_quantity;

                    $is_update=false;
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$this->customer->id);
                    }
                }
                //purchase
                else if($data->add_gallon_purchase_type=="purchase"){
                    $sold_gallon->quantity += $data->add_gallon_quantity;
                    $is_update=false;
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$this->customer->id);
                    }
                }
                //non_ervill
                else if($data->add_gallon_purchase_type=="non_ervill"){                  
                    $non_ervill_gallon->quantity += $data->add_gallon_quantity;
                    $is_update=false;
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$this->customer->id);
                    }
                }
                           

                //update additional quantity and purchase type, filled gallon
                $filled_gallon->quantity -= ($data->add_gallon_quantity - $this->additional_quantity);
                $this->purchase_type = $data->add_gallon_purchase_type;
                $this->additional_quantity = $data->add_gallon_quantity;

            }else{
                //no additional gallon
                $filled_gallon->quantity += $this->additional_quantity;
                $this->purchase_type = null;
                $this->additional_quantity = 0;
            }
            
            $this->empty_gallon_quantity = $data->quantity;
        }
       

        $this->order->quantity = $data->quantity;
        
        if(!$this->shipment_id){
            $this->delivery_at = $data->delivery_at;
        }
        else if($data->remove_shipment){
            $this->shipment_id = null;
        }
        // $this->status = $data->status;
        //$this->customer_id = $data->customer_id;

        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() || !$outgoing_gallon->save() || !$non_ervill_gallon->save() || !$sold_gallon->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }
        return ($this->save());
    }

    public function doAddToEditHistory($old_data, $data){
        //set old values
        
        $old_data['customer_name'] = $old_data['customer']['name'];
        $old_data['quantity'] = $old_data['order']['quantity'];
        $old_data['delivery_at'] = Carbon::parse($old_data['delivery_at'])->format('Y-n-d');

        $isShipped = false;
        if($old_data['shipment_id']){
            $isShipped = true;
        }

        unset($old_data['id']);
        unset($old_data['shipment_id']);
        unset($old_data['customer_id']);
        unset($old_data['order_id']);
        unset($old_data['order']);
        unset($old_data['customer']);

        $old_value = '';
        $old_value .= $old_data['oc_header_invoice_id'] . ';';
        $old_value .= $old_data['quantity'] . ';';
        $old_value .= $old_data['additional_quantity']. ';';
        $old_value .= $old_data['purchase_type']. ';';
       
        // if($isShipped){
        //     $old_value .= $old_data['customer_name'];
        // }
        // else{
            $old_value .= $old_data['delivery_at']. ';';
            $old_value .= $old_data['customer_name'];
        //}

        //set new values
        $new_value_obj = $data->toArray();
        $new_customer = Customer::find($data->customer_id);
        $new_value_obj['customer_name'] = $new_customer->name;
        unset($new_value_obj['id']);
        unset($new_value_obj['customer-table_length']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        $new_value = '';
        $new_value .= $new_value_obj['nomor_struk'] . ';'; 
        $new_value .= $new_value_obj['quantity'] . ';';    
        if($new_value_obj['add_gallon_quantity'] == null){
            $new_value_obj['add_gallon_quantity'] = 0;
        }
        $new_value .= $new_value_obj['add_gallon_quantity'] . ';';
        if($new_value_obj['purchase_type'] == null){
            $new_value .= $new_value_obj['add_gallon_purchase_type'] . ';';
        }else{
            $new_value .= $new_value_obj['purchase_type'] . ';';
        }
             
        // if($isShipped){
        //     $new_value .= $new_value_obj['customer_name'];
        // }
        // else{
            $new_value .= $new_value_obj['delivery_at']. ';';
            $new_value .= $new_value_obj['customer_name'];
        //}
       

        $edit_data = array(
            'module_name' => 'Order Customer',
            'data_id' => $data->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => auth()->id()
        );

        return EditHistory::create($edit_data);
    }

    public function doDelete($description, $author_id){
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);
        $outgoing_gallon = Inventory::find(5);
        $non_ervill_gallon = Inventory::find(6);
        $sold_gallon = Inventory::find(7);

        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Refund Gallon"){
                    $broken_gallon->quantity -= $issue->quantity;
                    $filled_gallon->quantity += $issue->quantity;
                }
                else if($issue->type == "Kesalahan Customer" ){
                    $broken_gallon->quantity -= $issue->quantity;
                    $empty_gallon->quantity += $issue->quantity;
                }else if($issue->type == "Cancel Transaction"){
                    $empty_gallon->quantity += $this->empty_gallon_quantity;
                    $filled_gallon->quantity -= $issue->quantity;
                    if($this->purchase_type=="rent"){
                        $outgoing_gallon->quantity += ($issue->quantity - $this->empty_gallon_quantity);
                    }else if($this->purchase_type=="non_ervill"){
                        $non_ervill_gallon->quantity += ($issue->quantity - $this->empty_gallon_quantity);
                    }
                    else if($this->purchase_type=="purchase"){
                        $sold_gallon->quantity += ($issue->quantity - $this->empty_gallon_quantity);
                    }
                    
                }
            }
        }

        $filled_gallon->quantity += ($this->order->quantity + $this->additional_quantity);
        $empty_gallon->quantity -= $this->empty_gallon_quantity;
        if($this->purchase_type=="rent"){
            $outgoing_gallon->quantity -= ($this->order->quantity + $this->additional_quantity - $this->empty_gallon_quantity);
        }else if($this->purchase_type=="non_ervill"){
            $non_ervill_gallon->quantity -= ($this->order->quantity + $this->additional_quantity - $this->empty_gallon_quantity);
        }else if($this->purchase_type=="purchase"){
            $sold_gallon->quantity -= ($this->order->quantity + $this->additional_quantity - $this->empty_gallon_quantity);
        }
        

        // if($empty_gallon->quantity<0){
        //     $empty_gallon->quantity = 0;
        // }
        // else if($broken_gallon->quantity<0){
        //     $broken_gallon->quantity = 0;
        // }
        // else if($filled_gallon->quantity<0){
        //     $filled_gallon->quantity = 0;
        // }

        if($this->is_new=='false') {
            if ($this->purchase_type) {
                if ($this->purchase_type == "rent") {
                    foreach ($this->customer->customerGallons as $customerGallon) {
                        if ($customerGallon->type == "rent") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                            $customerGallon->qty -= $this->additional_quantity;

                            if ($customerGallon->qty == 0) {
                                $customerGallon->delete();

                            } else {
                                $customerGallon->save();

                            }

                            break;
                        }
                    }
                } else if ($this->purchase_type == "purchase") {
                    foreach ($this->customer->customerGallons as $customerGallon) {
                        if ($customerGallon->type == "purchase") {
                            $customerGallon->qty -= $this->additional_quantity;
                            if ($customerGallon->qty == 0) {
                                $customerGallon->delete();
                            } else {
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                } else if ($this->purchase_type == "non_ervill") {
                    foreach ($this->customer->customerGallons as $customerGallon) {
                        if ($customerGallon->type == "non_ervill") {
//                        $non_ervill_gallon->quantity -= $this->additional_quantity;
                            $customerGallon->qty -= $this->additional_quantity;
                            if ($customerGallon->qty == 0) {
                                $customerGallon->delete();
                            } else {
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }

            }
        }else if($this->is_new=='true'){
            if ($this->purchase_type) {
                if ($this->purchase_type == "rent") {
                    foreach ($this->customer->customerGallons as $customerGallon) {
                        if ($customerGallon->type == "rent") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                            $customerGallon->qty -= $this->order->quantity;

                            if ($customerGallon->qty == 0) {
                                $customerGallon->delete();

                            } else {
                                $customerGallon->save();

                            }

                            break;
                        }
                    }
                } else if ($this->purchase_type == "purchase") {
                    foreach ($this->customer->customerGallons as $customerGallon) {
                        if ($customerGallon->type == "purchase") {
                            $customerGallon->qty -= $this->order->quantity;
                            if ($customerGallon->qty == 0) {
                                $customerGallon->delete();
                            } else {
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                } else if ($this->purchase_type == "non_ervill") {
                    foreach ($this->customer->customerGallons as $customerGallon) {
                        if ($customerGallon->type == "non_ervill") {
//                        $non_ervill_gallon->quantity -= $this->additional_quantity;
                            $customerGallon->qty -= $this->order->quantity;
                            if ($customerGallon->qty == 0) {
                                $customerGallon->delete();
                            } else {
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }

            }
        }

        if(!$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() || !$outgoing_gallon->save() || !$non_ervill_gallon->save() || !$sold_gallon->save() ||!$this->doAddToDeleteHistory($description, $author_id)){
            return false;
        }
        return $this->order->doDelete();
    }

    public function doAddToDeleteHistory($description, $author_id){
        $data = array(
            'module_name' => 'Order Customer',
            'description' => $description,
            'data_id' => $this->id,
            'user_id' => $author_id
        );

        return DeleteHistory::create($data);
    }

    public function doRestore(){
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);
        $outgoing_gallon = Inventory::find(5);
        $non_ervill_gallon = Inventory::find(6);
        $sold_gallon = Inventory::find(7);

        if($this->order->issues){
            foreach($this->order->issues as $issue){
                if($issue->type == "Refund Gallon"){
                    $broken_gallon->quantity += $issue->quantity;
                    $filled_gallon->quantity -= $issue->quantity;
                }
                else if($issue->type == "Kesalahan Customer" ){
                    $broken_gallon->quantity += $issue->quantity;
                    $empty_gallon->quantity -= $issue->quantity;
                }else if($issue->type == "Cancel Transaction"){
                    $empty_gallon->quantity -= $this->empty_gallon_quantity;
                    $filled_gallon->quantity += $issue->quantity;
                    if($this->purchase_type=="rent"){
                        $outgoing_gallon->quantity -= ($issue->quantity - $this->empty_gallon_quantity);
                    }else if($this->purchase_type=="non_ervill"){
                        $non_ervill_gallon->quantity -= ($issue->quantity - $this->empty_gallon_quantity);
                    }
                    else if($this->purchase_type=="purchase"){
                        $sold_gallon->quantity -= ($issue->quantity - $this->empty_gallon_quantity);
                    }
                    
                }
            }
        }
        $filled_gallon->quantity -= ($this->order->quantity + $this->additional_quantity);
        $empty_gallon->quantity += $this->empty_gallon_quantity;
        if($this->purchase_type=="rent"){
            $outgoing_gallon->quantity += ($this->order->quantity + $this->additional_quantity - $this->empty_gallon_quantity);
        }else if($this->purchase_type=="non_ervill"){
            $non_ervill_gallon->quantity += ($this->order->quantity + $this->additional_quantity - $this->empty_gallon_quantity);
        }
        else if($this->purchase_type=="purchase"){
            $sold_gallon->quantity += ($this->order->quantity + $this->additional_quantity - $this->empty_gallon_quantity);
        }
        

        // if($empty_gallon->quantity<0){
        //     $empty_gallon->quantity = 0;
        // }
        // else if($broken_gallon->quantity<0){
        //     $broken_gallon->quantity = 0;
        // }
        // else if($filled_gallon->quantity<0){
        //     $filled_gallon->quantity = 0;
        // }

        if($this->is_new=='false') {
            if ($this->purchase_type) {
                if ($this->purchase_type == "rent") {
                    if(count($this->customer->customerGallons)>0){
                        foreach ($this->customer->customerGallons as $customerGallon) {
                            if ($customerGallon->type == "rent") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty += $this->additional_quantity;

                                $customerGallon->save();

                                break;
                            }
                        }
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->additional_quantity;
                        $customerGallonNew->type="rent";
                        $customerGallonNew->save();
                    }else{
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->additional_quantity;
                        $customerGallonNew->type="rent";
                        $customerGallonNew->save();
                    }

                } else if ($this->purchase_type == "purchase") {
                    if(count($this->customer->customerGallons)>0){
                        foreach ($this->customer->customerGallons as $customerGallon) {
                            if ($customerGallon->type == "purchase") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty += $this->additional_quantity;

                                $customerGallon->save();

                                break;
                            }
                        }
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->additional_quantity;
                        $customerGallonNew->type="purchase";
                        $customerGallonNew->save();
                    }else{
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->additional_quantity;
                        $customerGallonNew->type="purchase";
                        $customerGallonNew->save();
                    }
                } else if ($this->purchase_type == "non_ervill") {
                    if(count($this->customer->customerGallons)>0){
                        foreach ($this->customer->customerGallons as $customerGallon) {
                            if ($customerGallon->type == "non_ervill") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty += $this->additional_quantity;

                                $customerGallon->save();

                                break;
                            }
                        }
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->additional_quantity;
                        $customerGallonNew->type="non_ervill";
                        $customerGallonNew->save();
                    }else{
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->additional_quantity;
                        $customerGallonNew->type="non_ervill";
                        $customerGallonNew->save();
                    }
                }

            }
        }else if($this->is_new=='true'){
            if ($this->purchase_type) {
                if ($this->purchase_type == "rent") {
                    if(count($this->customer->customerGallons)>0){
                        foreach ($this->customer->customerGallons as $customerGallon) {
                            if ($customerGallon->type == "rent") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty += $this->order->quantity;

                                $customerGallon->save();

                                break;
                            }
                        }
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->order->quantity;
                        $customerGallonNew->type="rent";
                        $customerGallonNew->save();
                    }else{
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->order->quantity;
                        $customerGallonNew->type="rent";
                        $customerGallonNew->save();
                    }
                } else if ($this->purchase_type == "purchase") {
                    if(count($this->customer->customerGallons)>0){
                        foreach ($this->customer->customerGallons as $customerGallon) {
                            if ($customerGallon->type == "purchase") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty += $this->order->quantity;

                                $customerGallon->save();

                                break;
                            }
                        }
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->order->quantity;
                        $customerGallonNew->type="purchase";
                        $customerGallonNew->save();
                    }else{
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->order->quantity;
                        $customerGallonNew->type="purchase";
                        $customerGallonNew->save();
                    }
                } else if ($this->purchase_type == "non_ervill") {
                    if(count($this->customer->customerGallons)>0){
                        foreach ($this->customer->customerGallons as $customerGallon) {
                            if ($customerGallon->type == "non_ervill") {
//                        $outgoing_gallon->quantity -= $this->additional_quantity;
                                $customerGallon->qty += $this->order->quantity;

                                $customerGallon->save();

                                break;
                            }
                        }
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->order->quantity;
                        $customerGallonNew->type="non_ervill";
                        $customerGallonNew->save();
                    }else{
                        $customerGallonNew= new CustomerGallon();
                        $customerGallonNew->customer_id=$this->customer->id;
                        $customerGallonNew->qty=$this->order->quantity;
                        $customerGallonNew->type="non_ervill";
                        $customerGallonNew->save();
                    }
                }

            }
        }

        if( !$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() || !$outgoing_gallon->save() || !$sold_gallon->save() || !$non_ervill_gallon->save() ){
            return false;
        }

        return $this->order->doRestore();
    }

    public function doForceDelete(){
        if(!$this->delete()){
            return false;
        }

        return $this->order->forceDelete();
    }

    public function doStartShipment(){
        $this->status = 'Proses';
        return $this->save();
    }

    public function doDropGallon(){

        if( count($this->order->issues) > 0 ){
            $this->status = 'Bermasalah';
        }else{
            $this->status = 'Selesai';
        }        
        return $this->save();
    }

    //need setting//
    public function doEditOrder($data)
    {
        //check if quantity is integer or not
        if( filter_var($data->gallon_qty, FILTER_VALIDATE_INT)===false || filter_var($data->empty_gallon_qty, FILTER_VALIDATE_INT)===false ){
            return false;
        }

        //check if quantity less than 0 or not
        if( filter_var($data->gallon_qty, FILTER_VALIDATE_INT)<0 || filter_var($data->empty_gallon_qty, FILTER_VALIDATE_INT)<0 ){
            return false;
        }

        //check if empty_gallon_qty exceeds gallon_qty
        if($data->empty_gallon_qty > $data->gallon_qty){
            return false;
        }

        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $outgoing_gallon = Inventory::find(5);
        $non_ervill_gallon = Inventory::find(6);

        $filled_stock = (integer)$filled_gallon->quantity+$this->order->quantity;

        if($filled_stock < $data->gallon_qty){
            return false;
        }

        $old_data = $this->toArray();


        //need to be adjusted with api//
        //recalculate inventory    
        $filled_gallon->quantity = ($filled_gallon->quantity + $this->order->quantity) - $data->gallon_qty;

        if($this->is_new=="true"){
            if($this->purchase_type=="rent"){
                //rent to rent
                if($data->purchase_type=="rent"){
                    $outgoing_gallon->quantity += ($data->gallon_qty - $this->order->quantity);
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $customerGallon->qty += ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();
                            break;
                        }
                    }
                }
                //rent to purchase
                else if($data->purchase_type=="purchase"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $outgoing_gallon->quantity -= $customerGallon->qty;   
                            $customerGallon->type="purchase";
                            $customerGallon->qty+= ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();
                            break;                    
                        }
                    }
                }    
                //rent to non_ervill
                else if($data->purchase_type=="non_ervill"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $outgoing_gallon->quantity -= $customerGallon->qty;         
                            $customerGallon->type="non_ervill";
                            $customerGallon->qty+= ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();   
                            $non_ervill_gallon->quantity += $customerGallon->qty;
                            break;            
                        }
                    }
                }            
            }else if($this->purchase_type=="purchase"){
                //purchase to rent
                if($data->purchase_type=="rent"){                    
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){
                            $customerGallon->type="rent";
                            $customerGallon->qty += ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();
                            $outgoing_gallon->quantity += $customerGallon->qty;
                            break;
                        }
                    }
                }
                //purchase to purchase
                else if($data->purchase_type=="purchase"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){            
                            $customerGallon->qty+= ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();
                            break;                    
                        }
                    }
                }    
                //purchase to non_ervill
                else if($data->purchase_type=="non_ervill"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){                                    
                            $customerGallon->type="non_ervill";
                            $customerGallon->qty+= ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();   
                            $non_ervill_gallon->quantity += $customerGallon->qty;
                            break;            
                        }
                    }
                }            
            }else if($this->purchase_type=="non_ervill"){
                //non_ervill to rent
                if($data->purchase_type=="rent"){                    
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){
                            $non_ervill_gallon->quantity -= $customerGallon->qty;
                            $customerGallon->type="rent";
                            $customerGallon->qty += ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();
                            $outgoing_gallon->quantity += $customerGallon->qty;
                            break;
                        }
                    }
                }
                //non_ervill to purchase
                else if($data->purchase_type=="purchase"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){  
                            $non_ervill_gallon->quantity -= $customerGallon->qty;  
                            $customerGallon->type="purchase";        
                            $customerGallon->qty+= ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();
                            break;                    
                        }
                    }
                }    
                //non_ervill to non_ervill
                else if($data->purchase_type=="non_ervill"){                   
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){                                   
                            $customerGallon->qty+= ($data->gallon_qty - $this->order->quantity);
                            $customerGallon->save();   
                            $non_ervill_gallon->quantity += ($data->gallon_qty - $this->order->quantity);
                            break;            
                        }
                    }
                }            
            }
            //update purchase type
            $this->purchase_type = $data->purchase_type;
        }else{
            $empty_gallon->quantity = ($empty_gallon->quantity - $this->empty_gallon_quantity) + $data->gallon_qty;
            //remove previoius additional gallon
            if($this->purchase_type){         
                if($this->purchase_type=="rent"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $outgoing_gallon->quantity -= $this->additional_quantity;
                            $customerGallon->qty -= $this->additional_quantity;         
                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }else if($this->purchase_type=="purchase"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){                            
                            $customerGallon->qty -= $this->additional_quantity;         
                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }else if($this->purchase_type=="non_ervill"){
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){   
                            $non_ervill_gallon->quantity -= $this->additional_quantity;    
                            $customerGallon->qty -= $this->additional_quantity;         
                            if($customerGallon->qty==0){
                                $customerGallon->delete();
                            }else{
                                $customerGallon->save();
                            }
                            break;
                        }
                    }
                }
                
            }

            //add gallon
            if($data->add_gallon){               
                //rent
                if($data->add_gallon_purchase_type=="rent"){
                    $outgoing_gallon->quantity += $data->add_gallon_quantity;

                    $is_update=false;
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="rent"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$this->customer->id);
                    }
                }
                //purchase
                else if($data->add_gallon_purchase_type=="purchase"){                  

                    $is_update=false;
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="purchase"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$this->customer->id);
                    }
                }
                //non_ervill
                else if($data->add_gallon_purchase_type=="non_ervill"){                  
                    $non_ervill_gallon->quantity += $data->add_gallon_quantity;
                    $is_update=false;
                    foreach($this->customer->customerGallons as $customerGallon){
                        if($customerGallon->type=="non_ervill"){
                            $customerGallon->qty += $data->add_gallon_quantity;
                            $customerGallon->save();
                            $is_update=true;
                            break;
                        }
                    }
                    if(!$is_update){
                        $newCustomerGallon = new CustomerGallon;
                        $newCustomerGallon->doMakeAdd($data,$this->customer->id);
                    }
                }
                           

                //update additional quantity and purchase type, filled gallon
                $filled_gallon->quantity -= ($data->add_gallon_quantity - $this->additional_quantity);
                $this->purchase_type = $data->add_gallon_purchase_type;
                $this->additional_quantity = $data->add_gallon_quantity;

            }else{
                //no additional gallon
                $filled_gallon->quantity -= $this->additional_quantity;
                $this->purchase_type = null;
                $this->additional_quantity = 0;
            }
            $this->empty_gallon_quantity = $data->gallon_qty;
        }
        
        

        // //need setting
        // $outgoing_gallon_change = ($data->gallon_qty - $this->order->quantity) - ($data->empty_gallon_qty - $this->empty_gallon_quantity);
        // $outgoing_gallon->quantity += $outgoing_gallon_change;

        

        $this->order->quantity = $data->gallon_qty;
        
        //$this->additional_quantity = $data->add_gallon_quantity;


        if(!$this->order->save() || !$empty_gallon->save() || !$filled_gallon->save() || !$outgoing_gallon->save() || !$non_ervill_gallon->save() || !$this->doAddToEditHistoryApi($old_data, $data, $this->id)){
            return false;
        }
        return ($this->save());
    }

    public function doAddToEditHistoryApi($old_data, $data, $order_customer_id){
        //set old values

        $old_data['quantity'] = $old_data['order']['quantity'];

        unset($old_data['id']);
        unset($old_data['shipment_id']);
        unset($old_data['customer_id']);
        unset($old_data['order_id']);
        unset($old_data['order']);
        unset($old_data['customer']);

        $old_value = '';
        $old_value .= $old_data['quantity'] . ';';
        $old_value .= $old_data['additional_quantity'] . ';';
        $old_value .= $old_data['purchase_type'];
      

        //set new values
        $new_value_obj = $data->toArray();

        unset($new_value_obj['id']);
        unset($new_value_obj['customer-table_length']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);

        $new_value = '';
        $new_value .= $new_value_obj['gallon_qty'] . ';';
        $new_value .= $new_value_obj['additional_quantity'] . ';';
        $new_value .= $new_value_obj['purchase_type'];
       

        $edit_data = array(
            'module_name' => 'Order Customer by Driver',
            'data_id' => $order_customer_id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => $data->user_id
        );

        return EditHistory::create($edit_data);
    }

    //test
    public function doUpdateStatus($status){
        $this->status = $status;
        return $this->save();
    }

    /**
     * Get the connection of the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection()
    {
        // TODO: Implement getQueueableConnection() method.
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        // TODO: Implement resolveRouteBinding() method.
    }
}
