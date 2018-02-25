<?php

use Illuminate\Database\Seeder;
use App\Models\Customer;

class KaryawanPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get Karyawan
        $customers = Customer::with(['ocHeaderInvoices' => function($query){
            $query->with('orderCustomers');
        }])
        ->whereIn('id', [27, 15, 26, 23])
        ->get();

        foreach($customers as $customer){
            foreach($customer->ocHeaderInvoices as $invoice){
                $invoice->description = "Harga air karyawan 10rb / galon";
                $invoice->additional_price = -2000;
                foreach($invoice->orderCustomers as $oc){
                    $oc->price -= 2000;
                    $oc->subtotal = $oc->quantity * $oc->price;
                    $oc->save();
                }
                $invoice->save();
            }
        }
    }
}
