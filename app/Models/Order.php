<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory;
use App\Models\Issue;
use App\Models\CustomerGallon;

class Order extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function doMakeOrderGallon($data, $author_id)
    {        
        $this->inventory_id = 1;
        $this->user_id = $author_id;
        $this->quantity = $data->quantity;
        $this->save();

        return $this;
    }

    public function doMakeOrderWater($data, $author_id)
    {        
        $this->inventory_id = 2;
        $this->user_id = $author_id;
        $this->quantity = $data->buffer_qty + $data->warehouse_qty;
        $this->save();

        return $this;
    }

    public function doMakeOrderCustomer($data, $customer_id, $author_id){
        $this->inventory_id = 3;
        $this->user_id = $author_id;
        $this->quantity = $data->quantity;

        $filled_gallon = Inventory::find(3);            
        $filled_gallon->quantity -= $data->quantity;

        if($data->new_customer){//new customer            
            if($data->purchase_type=='rent'){
                $outgoing_gallon = Inventory::find(5);
                $outgoing_gallon->quantity += $data->quantity;
                if( !$outgoing_gallon->save() ){
                    return false;
                }  
            }else if($data->purchase_type=='non_ervill'){
                $non_ervill_gallon = Inventory::find(6);
                $non_ervill_gallon->quantity += $data->quantity;
                if( !$non_ervill_gallon->save() ){
                    return false;
                } 
            }
            else if($data->purchase_type=='purchase'){
                $sold_gallon = Inventory::find(7);
                $sold_gallon->quantity += $data->quantity;
                if(!$sold_gallon->save()){
                    return false;
                }
            }
        }else{//existing customer
            $empty_gallon = Inventory::find(2);
            $empty_gallon->quantity += $data->quantity;

            //add gallon
            if($data->add_gallon){
                //create or update customerGallon
                $existingCustomerGallon = CustomerGallon::where([
                    ['customer_id',$customer_id], 
                    ['type',$data->add_gallon_purchase_type]])->first();

                if( $existingCustomerGallon ){
                    $existingCustomerGallon->qty += $data->add_gallon_quantity;
                    if( !$existingCustomerGallon->save() ){
                        return false;
                    } 
                }else{
                    $customerGallonAdd = new CustomerGallon;
                    if(!$customerGallonAdd->doMakeAdd($data, $customer_id)){
                        return false;
                    }  
                }

                //recalculate inventory for more gallon added
                $filled_gallon->quantity -= $data->add_gallon_quantity;
                if($data->add_gallon_purchase_type=='rent'){
                    $outgoing_gallon = Inventory::find(5);
                    $outgoing_gallon->quantity += $data->add_gallon_quantity;
                    if( !$outgoing_gallon->save() ){
                        return false;
                    }  
                }else if($data->add_gallon_purchase_type=='non_ervill'){
                    $non_ervill_gallon = Inventory::find(6);
                    $non_ervill_gallon->quantity += $data->add_gallon_quantity;
                    if( !$non_ervill_gallon->save() ){
                        return false;
                    } 
                }
                else if($data->add_gallon_purchase_type=='purchase'){
                    $sold_gallon = Inventory::find(7);
                    $sold_gallon->quantity += $data->quantity;
                    if( !$sold_gallon->save() ){
                        return false;
                    }
                }
                
                //$this->quantity += $data->add_gallon_quantity;
            }

            if(!$empty_gallon->save()){
                return false;
            }
        }

        if( !$filled_gallon->save() ){
            return false;
        }  

        $this->save();

        return $this;
    }


    public function doDelete(){
        return $this->delete();
    }

    public function doRestore(){
        return $this->restore();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function inventory()
    {
        return $this->belongsTo('App\Models\Inventory');
    }
    public function issues(){
        return $this->hasMany('App\Models\Issue');
    }
    public function orderGallon()
    {
        return $this->hasOne('App\Models\OrderGallon');
    }
    public function orderWater()
    {
        return $this->hasOne('App\Models\OrderWater');
    }
    public function orderCustomer()
    {
        return $this->hasOne('App\Models\OrderCustomer');
    }
}
