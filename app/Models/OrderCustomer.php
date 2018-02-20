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

    public function priceMaster(){
        return $this->belongsTo('App\Models\Price', 'price_id');
    }

    public function ocHeaderInvoice(){
        return $this->belongsTo('App\Models\OcHeaderInvoice');
    }

    public function doMake($data, $invoice_no, $additional_price = null)
    {
        $price = Price::find($data->price_id);
        $this->oc_header_invoice_id = $invoice_no;
        $this->name = $price->name;
        if($additional_price){
            $this->price = $price->price + $additional_price;
        }
        else{
            $this->price = $price->price;
        }
        $this->price_id = $data->price_id;
        $this->quantity = $data->qty;
        $this->subtotal = $this->quantity * $this->price;

        return $this->save();
    }

    /////////api////////////
    public function doCancel(){
        $empty_gallon = Inventory::find(2);
        $filled_gallon = Inventory::find(3);
        $broken_gallon = Inventory::find(4);
        $outgoing_gallon = Inventory::find(5);
        $non_ervill_gallon = Inventory::find(6);
        $sold_gallon = Inventory::find(7);

        // if($this->order->issues){
        //     foreach($this->order->issues as $issue){
        //         if($issue->type == "Refund Gallon"){
        //             $broken_gallon->quantity -= $issue->quantity;
        //             $filled_gallon->quantity += $issue->quantity;
        //         }
        //         else if($issue->type == "Kesalahan Customer" ){
        //             $broken_gallon->quantity -= $issue->quantity;
        //             $empty_gallon->quantity += $issue->quantity;
        //         }else if($issue->type == "Cancel Transaction"){
        //             $empty_gallon->quantity += $this->empty_gallon_quantity;
        //             $filled_gallon->quantity -= $issue->quantity;
        //             if($this->purchase_type=="rent"){
        //                 $outgoing_gallon->quantity += ($issue->quantity - $this->empty_gallon_quantity);
        //             }else if($this->purchase_type=="non_ervill"){
        //                 $non_ervill_gallon->quantity += ($issue->quantity - $this->empty_gallon_quantity);
        //             }
        //             else if($this->purchase_type=="purchase"){
        //                 $sold_gallon->quantity += ($issue->quantity - $this->empty_gallon_quantity);
        //             }
                    
        //         }
        //     }
        // }

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

        if(!$filled_gallon->save() || !$empty_gallon->save() || !$broken_gallon->save() || !$outgoing_gallon->save() || !$non_ervill_gallon->save() || !$sold_gallon->save() ){
            return false;
        }
        return true;
    }

    //test
    // public function doUpdateStatus($status){
    //     $this->status = $status;
    //     return $this->save();
    // }

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
