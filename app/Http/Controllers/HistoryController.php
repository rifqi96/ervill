<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EditHistory;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->data['module'] = 'history';
    }

    public function showEdit(){
        $this->data['slug'] = 'edit_history';

        $this->data['breadcrumb'] = "History - Edit";

        $edit_histories = EditHistory::with('user')->get();

        foreach($edit_histories as $edit_history){
            $old_value = explode(";", $edit_history->old_value);
            $new_value = explode(";", $edit_history->new_value);
            $old_value_arr = array();
            $new_value_arr = array();

            if($edit_history->module_name == "User Management"){                             
                $old_value_arr['Username'] = $old_value[0];
                $old_value_arr['Nama'] = $old_value[1];
                $old_value_arr['Email'] = $old_value[2];
                $old_value_arr['No. Telepon'] = $old_value[3];
                $old_value_arr['Role'] = $old_value[4];
             
                $new_value_arr['Username'] = $new_value[0];
                $new_value_arr['Nama'] = $new_value[1];
                $new_value_arr['Email'] = $new_value[2];
                $new_value_arr['No. Telepon'] = $new_value[3];
                $new_value_arr['Role'] = $new_value[4];

            }else if($edit_history->module_name == "Outsourcing Driver"){             
                $old_value_arr['Nama'] = $old_value[0];
                
                $new_value_arr['Nama'] = $new_value[0];
            }else if($edit_history->module_name == "Outsourcing Water"){              
                $old_value_arr['Nama'] = $old_value[0];
                
                $new_value_arr['Nama'] = $new_value[0];
            }else if($edit_history->module_name == "Order Gallon"){             
                $old_value_arr['Outsourcing Pengemudi'] = $old_value[0];
                $old_value_arr['Jumlah (Galon)'] = $old_value[1];
                $old_value_arr['Tgl Order'] = $old_value[2];
                $old_value_arr['Tgl Penerimaan'] = $old_value[3];
                
                $new_value_arr['Outsourcing Pengemudi'] = $new_value[0];
                $new_value_arr['Jumlah (Galon)'] = $new_value[1];
                $new_value_arr['Tgl Order'] = $new_value[2];
                $new_value_arr['Tgl Penerimaan'] = $new_value[3];
            }

            $edit_history->old_value = $old_value_arr;
            $edit_history->new_value = $new_value_arr;
        }

        $this->data['edit_history'] = $edit_histories;
        //dd($this->data['edit_history']);
        return view('history.edit', $this->data);
    }

    public function showDelete(){
        $this->data['slug'] = 'delete_history';

        $this->data['breadcrumb'] = "History - Delete";

        return view('history.delete', $this->data);
    }

    public function getEditHistories(){
        $editHistories = EditHistory::all();
        return json_encode($editHistories);
    }
}
