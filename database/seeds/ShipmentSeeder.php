<?php

use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\OcHeaderInvoice;
use App\Models\ReHeaderInvoice;
use Carbon\Carbon;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ocs = OcHeaderInvoice::orderBy('payment_date', 'asc')->get();
        $res = ReHeaderInvoice::orderBy('payment_date', 'asc')->get();

        $payment_dates = collect();
        // Getting Payment Dates
        foreach($ocs as $oc){
            $payment_dates->push(['date' => Carbon::parse($oc->payment_date)->format('Y-m-d')]);
        }
        foreach($res as $re){
            $payment_dates->push(['date' => Carbon::parse($re->payment_date)->format('Y-m-d')]);
        }

        $groupped_payment_dates = collect();
        $payment_dates->groupBy('date')->each(function($item, $key) use ($groupped_payment_dates){
            $groupped_payment_dates->push($key);
        });

        // Create Shipment rows and Updating ocs and res
        $id = 1;
        foreach($groupped_payment_dates as $payment_date){
            $shipment = new Shipment();
            $shipment->id = $id;
            $shipment->user_id = 5;
            $shipment->delivery_at = $payment_date;
            $shipment->status = 'Selesai';
            $shipment->save();

            foreach($ocs as $oc){
                if(Carbon::parse($oc->payment_date)->format('Y-m-d') == $payment_date){
                    $oc->shipment_id = $shipment->id;
                    $oc->save();
                }
            }

            foreach($res as $re){
                if(Carbon::parse($re->payment_date)->format('Y-m-d') == $payment_date){
                    $re->shipment_id = $shipment->id;
                    $re->save();
                }
            }

            $id++;
        }
    }
}
