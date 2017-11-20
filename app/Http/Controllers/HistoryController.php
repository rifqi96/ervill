<?php

namespace App\Http\Controllers;

use App\Models\DeleteHistory;
use Illuminate\Http\Request;
use App\Models\EditHistory;
use App\Models\User;
use App\Models\OutsourcingDriver;
use App\Models\Order;
use App\Models\OrderGallon;
use App\Models\Customer;
use League\CLImate\TerminalObject\Basic\Out;
use Illuminate\Support\Collection;

class HistoryController extends Controller
{
    /*
     * Available Module Names:
     * 1. User Management
     * 2. Outsourcing Driver
     * 3. Outsourcing Water
     * 4. Order Gallon
     * 5. Order Water
     * 6. Order Customer
     * 7. Customers
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->data['module'] = 'history';
    }

    /*======= View Methods =======*/
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

            }
            else if($edit_history->module_name == "Customers"){
                $old_value_arr['Nama'] = $old_value[0];
                $old_value_arr['Alamat'] = $old_value[1];
                $old_value_arr['No. Telepon'] = $old_value[2];

                $new_value_arr['Nama'] = $new_value[0];
                $new_value_arr['Alamat'] = $new_value[1];
                $new_value_arr['No. Telepon'] = $new_value[2];
            }
            else if($edit_history->module_name == "Outsourcing Driver"){
                $old_value_arr['Nama'] = $old_value[0];
                
                $new_value_arr['Nama'] = $new_value[0];
            }else if($edit_history->module_name == "Outsourcing Water"){              
                $old_value_arr['Nama'] = $old_value[0];
                
                $new_value_arr['Nama'] = $new_value[0];
            }else if($edit_history->module_name == "Order Gallon"){             
                $old_value_arr['Outsourcing Pengemudi'] = $old_value[1];
                $old_value_arr['Nama Pengemudi'] = $old_value[0];
                $old_value_arr['Jumlah (Galon)'] = $old_value[2];
                
                $new_value_arr['Outsourcing Pengemudi'] = $new_value[0];
                $new_value_arr['Nama Pengemudi'] = $new_value[1];
                $new_value_arr['Jumlah (Galon)'] = $new_value[2];
            }else if($edit_history->module_name == "Inventory"){             
                $old_value_arr['Jumlah (Galon)'] = $old_value[0];
                $old_value_arr['Harga'] = $old_value[1];
                
                $new_value_arr['Jumlah (Galon)'] = $new_value[0];
                $new_value_arr['Harga'] = $new_value[1];
            }else if($edit_history->module_name == "Order Water"){   
                $old_value_arr['Outsourcing Pabrik Air'] = $old_value[2];          
                $old_value_arr['Outsourcing Pengemudi'] = $old_value[3];
                $old_value_arr['Nama Pengemudi'] = $old_value[0];
                $old_value_arr['Jumlah (Galon)'] = $old_value[4];
                $old_value_arr['Tgl Pengiriman'] = $old_value[1];

                $new_value_arr['Outsourcing Pabrik Air'] = $new_value[0];          
                $new_value_arr['Outsourcing Pengemudi'] = $new_value[1];
                $new_value_arr['Nama Pengemudi'] = $new_value[2];
                $new_value_arr['Jumlah (Galon)'] = $new_value[3];
                $new_value_arr['Tgl Pengiriman'] = $new_value[4];
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

        $delete_histories = DeleteHistory::with('user')->get();

        foreach($delete_histories as $dh){
            $object = $this->getTrashedObject($dh);
            $dh->data_id = $object;
        }

        $this->data['delete_histories'] = $delete_histories;

        return view('history.delete', $this->data);
    }

    /*======= Get Methods =======*/
    public function getEditHistories(){
        $editHistories = EditHistory::all();
        return json_encode($editHistories);
    }

    public function getTrashedObject($request, $mode = ""){
        if($request->delete_id){
           $delete_id = $request->delete_id;
        }
        else{
            $delete_id = $request->id;
        }

        $dh = DeleteHistory::find($delete_id);

        if($dh->module_name == "User Management"){
            return User::onlyTrashed()
                ->find($dh->data_id);
        }
        else if($dh->module_name == "Customers"){
            return Customer::onlyTrashed()
                ->find($dh->data_id);
        }
        else if($dh->module_name == "Outsourcing Driver"){
            return OutsourcingDriver::onlyTrashed()
                ->find($dh->data_id);
        }
        else if($dh->module_name == "Order Gallon"){
            $order_gallon = OrderGallon::with(['order' => function($query){
                $query->onlyTrashed();
            }])
                ->where('order_id', $dh->data_id)
                ->first();

            $order = Order::onlyTrashed()
                ->with('user')
                ->find($dh->data_id);

            if($mode == '' || empty($mode)){
                $new_attributes = array(
                    'Admin' => $order->user->full_name
                );
                $order->fill($new_attributes);
                $order->setAttribute('id', $order_gallon->id);
                $order->makeHidden(['inventory_id', 'user']);
            }

            return $order;
        }
    }

    /*======= Do Methods =======*/
    public function doRestore(Request $request){
        $object = $this->getTrashedObject($request, 'restore');

        return ($object->restore() && DeleteHistory::destroy($request->delete_id));
    }

    public function doForceDelete(Request $request){
        $object = $this->getTrashedObject($request, 'delete');

        return ($object->doForceDelete() && DeleteHistory::destroy($request->delete_id));
    }

    public function doRestoreOrDelete(Request $request){
        if($request->submit_btn == "restore"){
            if($this->doRestore($request)){
                return back()->with('success', 'Data telah berhasil dikembalikan');
            }
            return back()->withErrors(['message' => 'Telah terjadi kesalahan dalam mengembalikan data']);
        }
        else if($request->submit_btn == "force_delete"){
            if($this->doForceDelete($request)){
                return back()->with('success', 'Data telah berhasil dihapus');
            }
            return back()->withErrors(['message' => 'Telah terjadi kesalahan dalam menghapus data']);
        }
        else{
            return back()->withErrors(['message' => 'Telah terjadi kesalahan']);
        }
    }

    public function doMassRestoreOrDelete(Request $request){
        $isSuccess = true;

        for($i=0; $i<count($request->ids); $i++){
            $request->delete_id = $request->ids[$i];

            if($request->submit_btn == "restore"){
                if(!$this->doRestore($request)){
                    $isSuccess = false;
                    break;
                }
            }
            else if($request->submit_btn == "force_delete"){
                if(!$this->doForceDelete($request)){
                    $isSuccess = false;
                    break;
                }
            }
            else{
                $isSuccess = false;
                break;
            }
        }

        if($request->submit_btn == "restore"){
            if($isSuccess){
                return back()->with('success', 'Data telah berhasil dikembalikan');
            }
            return back()->withErrors(['message' => 'Telah terjadi kesalahan dalam mengembalikan data']);
        }
        else if($request->submit_btn == "force_delete"){
            if($isSuccess){
                return back()->with('success', 'Data telah berhasil dihapus');
            }
            return back()->withErrors(['message' => 'Telah terjadi kesalahan dalam menghapus data']);
        }
        else{
            return back()->withErrors(['message' => 'Telah terjadi kesalahan']);
        }
    }
}
