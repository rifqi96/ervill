<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\EditHistory;
use App\Models\DeleteHistory;

class CustomerController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['slug'] = 'customers';
    }

    public function index()
    {
        $this->data['breadcrumb'] = "Setting - Customer";

        return view('setting.customers.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Setting - Customer - Create";

        return view('setting.customers.make', $this->data);
    }

    public function doMake(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'phone' => 'required|string|digits_between:3,14',
            'address' => 'required|string',
        ]);

        $customer = new Customer();

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
        $customer = Customer::select('id','name','address','phone')->find($request->id);

        $this->validate($request, [
            'name' => 'required|string',
            'phone' => 'required|string|digits_between:3,14',
            'address' => 'required|string',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

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
            'module_name' => 'Customers',
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
        $customer = Customer::find($request->data_id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $data = array(
            'module_name' => 'Customers',
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
        return Customer::all()->toJson();
    }
}
