<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderCustomer;
use App\Models\User;
use App\Models\EditHistory;
use App\Models\DeleteHistory;
use Carbon\Carbon;

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

    public function doMake($data){
        $driver = User::with('role')
            ->whereHas('role', function ($query){
                $query->where('name', 'driver');
            })
            ->find($data->driver_id);

        if(!$driver){
            return false;
        }

        $this->user_id = $driver->id;
        $this->delivery_at = $data->delivery_at;
        $this->status = 'Draft';

        if($data->order_ids){
            if(!$this->save()){
                return false;
            }

            return $this->doAddOrderToShipment($data);
        }

        return $this->save();
    }

    public function doAddOrderToShipment($data){
        foreach($data->order_ids as $order_id){
            $oc = OrderCustomer::with('order')
                ->whereHas('order', function($query){
                    $query->where('accepted_at', null);
                })
                ->find($order_id);

            if(Carbon::parse($this->delivery_at)->format('Y-m-d') != Carbon::parse($oc->delivery_at)->format('Y-m-d')){
                return false;
            }

            $oc->shipment_id = $this->id;
            if(!$oc->save()){
                return false;
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
            return false;
        }

        $old_data = $this->toArray();

        $this->user_id = $data->driver_id;
        $this->delivery_at = $data->delivery_at;
        $this->status = $data->status;

        if(!$this->save() || !$this->doAddToEditHistory($old_data, $data)){
            return false;
        }

        foreach($this->orderCustomers as $oc){
            $oc->delivery_at = $this->delivery_at;
            $oc->status = $this->status;
            if(!$oc->save()){
                return false;
            }
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

        if(!$this->delete() || !DeleteHistory::create($data)) {
            return false;
        }

        return true;
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
