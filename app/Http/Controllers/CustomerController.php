<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\EditHistory;
use App\Models\DeleteHistory;

class CustomerController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'customers';
    }

    public function index()
    {
        $this->data['breadcrumb'] = "Home - Customer";
        $this->data['slug'] = 'list';

        return view('setting.customers.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Home - Customer - Create";
        $this->data['slug'] = 'list';

        return view('setting.customers.make', $this->data);
    }

    public function showOverdue(){
        $this->data['breadcrumb'] = "Home - Customer Overdue";
        $this->data['slug'] = 'overdue';

        $this->data['customers'] = $this->getOverdueCustomers();

        return view('setting.customers.overdue', $this->data);
    }

    public function showDetails($id){
        $this->data['breadcrumb'] = "Home - Detail Customer";
        $this->data['slug'] = 'list';

        $this->data['customer'] = $this->get($id);

        return view('setting.customers.details', $this->data);
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
            'type' => 'required|in:end_customer,agent',
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
        $customer = Customer::select('id','name','address','phone', 'type')->find($request->id);

        $this->validate($request, [
            'type' => 'required|in:end_customer,agent',
            'name' => 'required|string',
            'phone' => 'required|string|digits_between:3,14',
            'address' => 'required|string',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        if($request->notif_day){
           $this->validate($request, [
               'notif_day' => 'required|integer'
           ]);
        }

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
        $customers = Customer::all();
        foreach($customers as $customer){
            $piutang = 0;
            foreach($customer->OcHeaderInvoices as $invoice){
                if(!$invoice->payment_date){
                    $piutang ++;
                }
            }
            $customer->piutang = $piutang;
        }

        return $customers;
    }

    public function get($id){
        return (new Customer())->get($id);
    }

    public function getOverdueCustomers(){
        return (new Customer())->getOverdueCustomers();
    }
}
