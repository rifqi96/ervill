<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\CustomerNonErvill;
use App\Models\EditHistory;
use App\Models\DeleteHistory;

class CustomerNonErvillController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'customerNonErvills';
    }

    public function index()
    {
        $this->data['breadcrumb'] = "Home - Customer Pihak Ketiga";
        $this->data['slug'] = 'listNonErvill';

        return view('setting.customerNonErvills.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Home - Customer Pihak Ketiga - Create";
        $this->data['slug'] = 'listNonErvill';

        return view('setting.customerNonErvills.make', $this->data);
    }

    public function showDetails($id){
        $this->data['breadcrumb'] = "Home - Detail Customer Pihak Ketiga";
        $this->data['slug'] = 'listNonErvill';

        $this->data['customer'] = CustomerNonErvill::find($id);

        return view('setting.customerNonErvills.details', $this->data);
    }

    public function doMake(Request $request)
    {
        if(!$request->phone){
            if($request->phone != '0'){
                $request->phone = '0000';
                $request['phone'] = '0000';
            }
        }

        $this->validate($request, [            
            'name' => 'required|string',
            'phone' => 'required|string|digits_between:3,14',
            'address' => 'required|string',
        ]);

        $customer = new CustomerNonErvill();

        if($customer->doMake($request)){
            return back()
                ->with('success', 'Data telah berhasil dibuat');
        }else{
            return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doUpdate(Request $request)
    {
        $customer = CustomerNonErvill::select('id','name','address','phone')->find($request->id);

        $this->validate($request, [           
            'name' => 'required|string',
            'phone' => 'required|string|digits_between:3,14',
            'address' => 'required|string',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        // if($request->notif_day){
        //    $this->validate($request, [
        //        'notif_day' => 'required|integer'
        //    ]);
        // }

        //set old values
        $old_value_obj = $customer->toArray();
        unset($old_value_obj['id']);
        $old_value = '';
        $i=0;
        foreach ($old_value_obj as $row) {
            if($i == count($old_value_obj)-1){
                $old_value .= $row;
            }else{
                $old_value .= $row.';';
            }
            $i++;
        }


        //set new values
        $new_value_obj = $request->toArray();
        unset($new_value_obj['id']);
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
            'module_name' => 'Customers Pihak Ketiga',
            'data_id' => $request->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description,
            'user_id' => auth()->id()
        );

        if($customer->doUpdate($request) && EditHistory::create($edit_data)){
            return back()
                ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada update data']);
        }
    }

    public function doDelete(Request $request){
        $customer = CustomerNonErvill::find($request->data_id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $data = array(
            'module_name' => 'Customers Pihak Ketiga',
            'description' => $request->description,
            'data_id' => $customer->id,
            'user_id' => auth()->user()->id
        );

        if($customer->doDelete() && DeleteHistory::create($data)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }


    public function getAll()
    {
        return CustomerNonErvill::all();
    }

    // public function get($id){
    //     return (new CustomerNonErvill())->get($id);
    // }
}
