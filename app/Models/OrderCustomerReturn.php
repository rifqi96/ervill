<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderCustomerReturn extends Model
{
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo('App\Models\Customer');
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
            return false;
        }

        $returned_gallons = $data->empty_quantity + $data->filled_quantity;

        if($returned_gallons > $customer_gallon->qty){
            return false;
        }

        $this->customer_id = $data->customer_id;
        $this->filled_gallon_quantity = $data->filled_quantity;
        $this->empty_gallon_quantity = $data->empty_quantity;
        $this->description = $data->description;
        $this->return_at = Carbon::parse($data->return_at)->format('Y-n-d');
        $this->author_id = $author_id;
        $this->status = 'Draft';

        return $this->save();
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

        $customer_gallon->qty -= $returned_gallons;

        if(!$customer_gallon->save()){
            return false;
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

        if(!$customer_gallon){
            return false;
        }

        $returned_gallons = $this->empty_gallon_quantity + $this->filled_gallon_quantity;

        $empty_gallon->quantity -= $this->empty_gallon_quantity;
        if(!$empty_gallon->save()){
            return false;
        }

        $filled_gallon->quantity -= $this->filled_gallon_quantity;
        if(!$filled_gallon->save()){
            return false;
        }

        $customer_gallon->qty += $returned_gallons;

        if(!$customer_gallon->save()){
            return false;
        }

        $this->status = 'Batal';

        return $this->save();
    }
}
