<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $guarded = [];

    public function doUpdate($data){
        if(!$this->doAddToEditHistory($this, $data)){
            return false;
        }

        $this->price = $data->price;

        return $this->save();
    }

    public function doAddToEditHistory($old_data, $data){
        $old_value = '';
        $old_value .= $old_data->name . ';';
        $old_value .= $old_data->price. ';';
        $old_value .= $old_data->customer_type. ';';

        //set new values
        $new_value = '';
        $new_value .= $old_data->name . ';';
        $new_value .= $data->price. ';';
        $new_value .= $old_data->customer_type. ';';

        $edit_history = new EditHistory();
        $edit_history->module_name = 'Price List';
        $edit_history->data_id = $old_data->id;
        $edit_history->old_value = $old_value;
        $edit_history->new_value = $new_value;
        $edit_history->description = $data->description;
        $edit_history->user_id = auth()->id();

        return $edit_history->save();
    }

    public function ocInvoices(){
        return $this->hasMany('App\Models\OrderCustomerInvoice');
    }

    public function ocBuyInvoices(){
        return $this->hasMany('App\Models\OrderCustomerBuyInvoice');
    }

    public function ocReturnInvoices(){
        return $this->hasMany('App\Models\OrderCustomerReturnInvoice');
    }
}
