<?php

use Illuminate\Database\Seeder;
use App\Models\OcHeaderInvoice;

class PaymentDatesFixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ocs = OcHeaderInvoice::all();
        foreach($ocs as $oc){
            if($oc->orderCustomerInvoices->count() > 0){
                $delivery_at = $oc->orderCustomerInvoices[0]->orderCustomer->delivery_at;

                $oc->payment_date = $delivery_at;
                $oc->save();
            }
        }
    }
}
