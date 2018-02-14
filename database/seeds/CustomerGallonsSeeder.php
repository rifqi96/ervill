<?php

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerGallon;

class CustomerGallonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = Customer::all();

        foreach($customers as $customer){
            $customer_gallons = CustomerGallon::where('customer_id', $customer->id)->get();

            foreach($customer_gallons as $gallon){
                if($gallon->type == "rent"){
                    $customer->rent_qty = $gallon->qty;
                }
                else if($gallon->type == "purchase"){
                    $customer->purchase_qty = $gallon->qty;
                }
                else if($gallon->type == "non_ervill"){
                    $customer->non_erv_qty = $gallon->qty;
                }
            }

            if(!$customer->rent_qty){
                $customer->rent_qty = 0;
            }
            if(!$customer->purchase_qty){
                $customer->purchase_qty = 0;
            }
            if(!$customer->non_erv_qty){
                $customer->non_erv_qty = 0;
            }
//            if(!$customer->notif_day){
//                $customer->notif_day = 14;
//            }

            $customer->save();
        }
    }
}
