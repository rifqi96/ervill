<?php

use Illuminate\Database\Seeder;
use App\Models\OrderCustomerInvoice;
use App\Models\OrderCustomerReturnInvoice;
use App\Models\OrderCustomerBuyInvoice;

class PriceNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $returns = OrderCustomerReturnInvoice::all();
        $ocs = OrderCustomerInvoice::all();
        $buys = OrderCustomerBuyInvoice::all();

        foreach($returns as $row){
            $price = $row->price->price;
            $row->price_number = $price;
            $row->save();
        }

        foreach($ocs as $row){
            $price = $row->price->price;
            $row->price_number = $price;
            $row->save();
        }

        foreach($buys as $row){
            $price = $row->price->price;
            $row->price_number = $price;
            $row->save();
        }
    }
}
