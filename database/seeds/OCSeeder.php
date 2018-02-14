<?php

use Illuminate\Database\Seeder;
use App\Models\OcHeaderInvoice;
use App\Models\OrderCustomer;
use App\Models\OrderCustomerBuy;
use App\Models\Price;

class OCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $headers = OcHeaderInvoice::all();
        $ocs = OrderCustomer::all();
        $ocbuys = OrderCustomerBuy::all();

        // OCHeader->delivery_at
        // OCHeader->user_id
        // OCHeader->customer_id
        foreach($headers as $header){
            $header->delivery_at = $header->orderCustomerInvoices[0]->orderCustomer->delivery_at;
            $header->user_id = $header->orderCustomerInvoices[0]->orderCustomer->order->user_id;
            $header->customer_id = $header->orderCustomerInvoices[0]->orderCustomer->customer_id;

            $header->save();
        }

        // OC->subtotal
        // OC->price
        // OC->quantity
        // OC->name
        // OC->oc_header_invoice_id

        //Removing existing OC
        foreach($ocs as $oc){
            $oc->order->delete();
            $oc->order->forceDelete();

            $oc->delete();
        }
        foreach($ocbuys as $ocbuy){
            $ocbuy->delete();
        }

        //Creating new OC
        $id = 1;
        foreach($headers as $header){
            foreach($header->orderCustomerInvoices as $oc_invoice){
                $price_master = Price::find($oc_invoice->price_id);
                $oc = new OrderCustomer;
                $oc->id = $id;
                $oc->oc_header_invoice_id = $header->id;
                $oc->price_id = $oc_invoice->price_id;
                $oc->name = $price_master->name;
                $oc->quantity = $oc_invoice->quantity;
                $oc->price = $oc_invoice->price_number;
                $oc->subtotal = $oc_invoice->subtotal;
                $oc->save();

                $id++;
            }

            foreach($header->orderCustomerBuyInvoices as $orderCustomerBuyInvoice){
                $price_master = Price::find($orderCustomerBuyInvoice->price_id);
                $oc = new OrderCustomer;
                $oc->id = $id;
                $oc->oc_header_invoice_id = $header->id;
                $oc->name = $price_master->name;
                $oc->price_id = $oc_invoice->price_id;
                $oc->quantity = $orderCustomerBuyInvoice->quantity;
                $oc->price = $orderCustomerBuyInvoice->price_number;
                $oc->subtotal = $orderCustomerBuyInvoice->subtotal;
                $oc->save();

                $id++;
            }
        }

    }
}
