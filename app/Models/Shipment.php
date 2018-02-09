<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderCustomer;
use App\Models\User;
use App\Models\EditHistory;
use App\Models\DeleteHistory;
use Carbon\Carbon;
use Validator;
use Illuminate\Validation\ValidationException;

class Shipment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function orderCustomers()
    {
        return $this->hasMany('App\Models\OrderCustomer');
    }
    public function ocHeaderInvoices()
    {
        return $this->hasMany('App\Models\OcHeaderInvoice');
    }
    public function reHeaderInvoices()
    {
        return $this->hasMany('App\Models\ReHeaderInvoice');
    }

    public function doMake($data){
        $driver = User::with('role')
            ->whereHas('role', function ($query){
                $query->where('name', 'driver');
            })
            ->find($data->driver_id);

        if(!$driver){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('driver', 'Driver tidak ditemukan');
            throw new ValidationException($validator);
        }

        // Faktur ID Validation //
        $second_ids = collect();
        foreach($data->order_ids as $order_id){
            if(!OcHeaderInvoice::find($order_id)){
                $second_ids->push($order_id);
            }
        }
        foreach($second_ids as $second_id){
            if(!ReHeaderInvoice::find($second_id)){
                $validator = Validator::make([], []); // Empty data and rules fields
                $validator->errors()->add('order_id', 'Data faktur tidak ditemukan');
                throw new ValidationException($validator);
            }
        }

        $this->user_id = $driver->id;
        $this->delivery_at = $data->delivery_at;
        $this->status = 'Draft';

        if($data->order_ids){
            if(!$this->save()){
                return false;
            }

            if(!$this->doAddOrderToShipment($data)){
                return false;
            }
        }

        return $this;
    }

    public function doAddOrderToShipment($data){
        // Faktur ID Validation //
        foreach($data->order_ids as $order_id){
            if($oc = OcHeaderInvoice::find($order_id)){
                if($oc->orderCustomerInvoices->count() > 0){
                    foreach($oc->orderCustomerInvoices as $oc_invoice){
                        if(Carbon::parse($this->delivery_at)->format('Y-m-d') != Carbon::parse($oc_invoice->orderCustomer->delivery_at)->format('Y-m-d')){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('delivery_at', 'Tanggal salah');
                            throw new ValidationException($validator);
                        }
                        if(!$oc_invoice->orderCustomer->save()){
                            return false;
                        }
                    }
                }

                if($oc->orderCustomerBuyInvoices->count() > 0){
                    foreach($oc->orderCustomerBuyInvoices as $oc_invoice){
                        if(Carbon::parse($this->delivery_at)->format('Y-m-d') != Carbon::parse($oc_invoice->orderCustomerBuy->buy_at)->format('Y-m-d')){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('delivery_at', 'Tanggal salah');
                            throw new ValidationException($validator);
                        }
                        if(!$oc_invoice->orderCustomerBuy->save()){
                            return false;
                        }
                    }
                }

                $oc->shipment_id = $this->id;
                if(!$oc->save()){
                    return false;
                }
            }
            else{
                $re = ReHeaderInvoice::find($order_id);
                if($re->orderCustomerReturnInvoices->count() > 0){
                    foreach($re->orderCustomerReturnInvoices as $re_invoice){
                        if(Carbon::parse($this->delivery_at)->format('Y-m-d') != Carbon::parse($re_invoice->orderCustomerReturn->return_at)->format('Y-m-d')){
                            $validator = Validator::make([], []); // Empty data and rules fields
                            $validator->errors()->add('delivery_at', 'Tanggal salah');
                            throw new ValidationException($validator);
                        }
                        if(!$re_invoice->orderCustomerReturn->save()){
                            return false;
                        }
                    }
                }
                $re->shipment_id = $this->id;
                if(!$re->save()){
                    return false;
                }
            }
        }

        return true;
    }

    public function doUpdate($data){
        $driver = User::with('role')
            ->whereHas('role', function ($query){
                $query->where('name', 'driver');
            })
            ->find($data->driver_id);

        if(!$driver){
            $validator = Validator::make([], []); // Empty data and rules fields
            $validator->errors()->add('driver_id', 'User driver tidak ditemukan');
            throw new ValidationException($validator);
        }

        $old_data = $this->toArray();

        $this->user_id = $data->driver_id;
        $this->delivery_at = $data->delivery_at;
        $this->status = $data->status;

        // for OC and OC BUY
        if($this->ocHeaderInvoices->count() > 0){
            foreach($this->ocHeaderInvoices as $ocHeaderInvoice){
                $ocHeaderInvoice->status = $this->status;
                // for OC
                foreach($ocHeaderInvoice->orderCustomerInvoices as $oc_invoice){
                    $oc_invoice->orderCustomer->delivery_at = $this->delivery_at;
                    if(!$oc_invoice->orderCustomer->save()){
                        return false;
                    }
                }
                // for OC BUY
                foreach($ocHeaderInvoice->orderCustomerBuyInvoices as $oc_invoice){
                    $oc_invoice->orderCustomerBuy->buy_at = $this->delivery_at;
                    if(!$oc_invoice->orderCustomerBuy->save()){
                        return false;
                    }
                }
                if(!$ocHeaderInvoice->save()){
                    return false;
                }
            }
        }

        // for OC Return
        if($this->reHeaderInvoices->count() > 0){
            foreach($this->reHeaderInvoices as $reHeaderInvoice){
                $reHeaderInvoice->status = $this->status;
                foreach($reHeaderInvoice->orderCustomerReturnInvoices as $oc_invoice){
                    $oc_invoice->orderCustomerReturn->return_at = $this->delivery_at;
                    if(!$oc_invoice->orderCustomerReturn->save()){
                        return false;
                    }
                }
                if(!$reHeaderInvoice->save()){
                    return false;
                }
            }
        }

        if(!$this->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }

        return true;
    }

    public function doDelete($data){
        $data = array(
            'module_name' => 'Shipment',
            'description' => $data->description,
            'data_id' => $data->shipment_id,
            'user_id' => auth()->user()->id
        );

        if($this->ocHeaderInvoices){
            foreach($this->ocHeaderInvoices as $ocHeaderInvoice){
                $ocHeaderInvoice->shipment_id = null;
                if(!$ocHeaderInvoice->save()){
                    return false;
                }
            }
        }

        if($this->reHeaderInvoices){
            foreach($this->reHeaderInvoices as $reHeaderInvoice){
                $reHeaderInvoice->shipment_id = null;
                if(!$reHeaderInvoice->save()){
                    return false;
                }
            }
        }

        if(!$this->delete() || !DeleteHistory::create($data)) {
            return false;
        }

        return true;
    }

    public function doStartShipment($user_id){

        $today = Carbon::today();

        //get any ongoing shipment
        $shipment_process = Shipment::where([
            ['user_id', $user_id],
            ['delivery_at',$today],
            ['status','Proses']])->first();

        //fail starting the shipment, because there is ongoing shipment
        if($shipment_process){
            return false;
        }

        $this->status = 'Proses';
        return $this->save();
    }

    public function doFinishShipment(){

        //check if there are still any ongoing shipment
        foreach($this->ocHeaderInvoices as $ocHeaderInvoice){
            if($ocHeaderInvoice->status == 'Proses'){
                return false;
            }
        }
        foreach($this->reHeaderInvoices as $reHeaderInvoice){
            if($reHeaderInvoice->status == 'Proses'){
                return false;
            }
        }

        $this->status = 'Selesai';
        return $this->save();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }

    public function doRestore(){
        return $this->restore();
    }

    public function doAddToEditHistory($old_data, $data){
        //set old values
        unset($old_data['id']);
        unset($old_data['track_data']);
        unset($old_data['created_at']);
        unset($old_data['updated_at']);
        unset($old_data['deleted_at']);
        $old_data['delivery_at'] = Carbon::parse($old_data['delivery_at'])->format('Y-n-d');
        $old_data['user_id'] = User::find($old_data['user_id'])->full_name;
        $old_value = '';
        $i=0;
        foreach ($old_data as $row) {
            if($i == count($old_data)-1){
                $old_value .= $row;
            }else{
                $old_value .= $row.';';
            }
            $i++;
        }


        //set new values
        $new_value_obj = $data->toArray();
        unset($new_value_obj['shipment_id']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        $new_value_obj['driver_id'] = User::find($new_value_obj['driver_id'])->full_name;
        $new_value = '';
        $i=0;
        foreach ($new_value_obj as $row) {
            if($i == count($new_value_obj)-1){
                $new_value .= $row;
            }else{
                $new_value .= $row.';';
            }
            $i++;
        }

        $edit_data = array(
            'module_name' => 'Shipment',
            'data_id' => $data->shipment_id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $data->description,
            'user_id' => auth()->id()
        );

        return EditHistory::create($edit_data);
    }
}
