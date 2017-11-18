<?php

namespace App\Http\Controllers;

use App\Models\DeleteHistory;
use Illuminate\Http\Request;
use App\Models\EditHistory;
use App\Models\User;
use App\Models\OutsourcingDriver;
use League\CLImate\TerminalObject\Basic\Out;

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
     * 7. ....
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

        $edit_histories = EditHistory::all();

        foreach($edit_histories as $edit_history){
            $old_value = explode(";", $edit_history->old_value);
            $new_value = explode(";", $edit_history->new_value);
            $old_value_arr = array();
            $new_value_arr = array();

            if($edit_history->module_name == "User Management"){
                $old_value_arr['ID'] = $old_value[0];
                $old_value_arr['Role ID'] = $old_value[1];
                $old_value_arr['Username'] = $old_value[2];
                $old_value_arr['Nama'] = $old_value[3];
                $old_value_arr['Email'] = $old_value[4];
                $old_value_arr['No. Telepon'] = $old_value[5];

                $new_value_arr['ID'] = $new_value[0];
                $new_value_arr['Role ID'] = $new_value[1];
                $new_value_arr['Username'] = $new_value[2];
                $new_value_arr['Nama'] = $new_value[3];
                $new_value_arr['Email'] = $new_value[4];
                $new_value_arr['No. Telepon'] = $new_value[5];
            }else if($edit_history->module_name == "Outsourcing Driver"){
                $old_value_arr['ID'] = $old_value[0];
                $old_value_arr['Nama'] = $old_value[1];

                $new_value_arr['ID'] = $new_value[0];
                $new_value_arr['Nama'] = $new_value[1];
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
            if($dh->module_name == "User Management"){
                $user = User::onlyTrashed()
                    ->where('id', $dh->data_id)
                    ->first();

                $dh->data_id = $user;
            }
        }

        $this->data['delete_histories'] = $delete_histories;

        return view('history.delete', $this->data);
    }

    /*======= Get Methods =======*/
    public function getEditHistories(){
        $editHistories = EditHistory::all();
        return json_encode($editHistories);
    }

    public function getTrashedObject($request){
        $dh = DeleteHistory::find($request->delete_id);

        if($dh->module_name == "User Management"){
            return User::onlyTrashed()
                ->find($dh->data_id);
        }
        else if($dh->module_name == "Outsourcing Driver"){
            return OutsourcingDriver::onlyTrashed()
                ->find($dh->data_id);
        }
    }

    /*======= Do Methods =======*/
    public function doRestore(Request $request){
        $object = $this->getTrashedObject($request);

        return ($object->restore() && DeleteHistory::destroy($request->delete_id));
    }

    public function doForceDelete(Request $request){
        $object = $this->getTrashedObject($request);

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
