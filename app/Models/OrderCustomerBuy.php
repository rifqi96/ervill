<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
            return false;
        }

        $customer_gallon_rent->qty -= $data->quantity;

        if(!$customer_gallon_purchase){
            $data->purchase_type = "purchase";
            $data['purchase_type'] = "purchase";
            if(!(new CustomerGallon)->doMake($data, $data->customer_id)){
                return false;
            }
        }
        else{
            $customer_gallon_purchase->qty += $data->quantity;
            if(!$customer_gallon_purchase->save()){
                return false;
            }
        }

        if(!$customer_gallon_rent->save()){
            return false;
        }

        $outgoing_gallon = Inventory::find(5);
        $sold_gallon = Inventory::find(7);

        if(!$outgoing_gallon || !$sold_gallon){
            return false;
        }

        $outgoing_gallon->quantity -= $data->quantity;
        $sold_gallon->quantity += $data->quantity;

        if(!$outgoing_gallon->save() || !$sold_gallon->save()){
            return false;
        }

        $this->customer_id = $data->customer_id;
        $this->no_struk = $data->no_struk;
        $this->quantity = $data->quantity;
        $this->author_id = $author_id;
        $this->buy_at = Carbon::parse($data->buy_at)->format('Y-n-d');

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

        if(!$customer_gallon_rent || !$customer_gallon_purchase){
            return false;
        }

        $customer_gallon_rent->qty += $this->quantity;
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
}
