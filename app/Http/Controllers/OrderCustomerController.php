<?php

namespace App\Http\Controllers;

use App\Models\OrderCustomerIssue;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderCustomer;
use App\Models\Inventory;
use App\Models\Customer;

class OrderCustomerController extends OrderController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['slug'] = 'customer';
    }

    /*======= Page Methods =======*/
     public function index()
    {
        $this->data['breadcrumb'] = "Order - Customer Order";

        return view('order.customer.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Customer Order - Create";

        $this->data['inventory'] = Inventory::find(2);

        return view('order.customer.make', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAll(){
        return OrderCustomer::with([
            'shipment' => function($query){
                $query->with(['user']);
            },
            'customer',
            'order' => function($query){
                $query->with(['user', 'issues']);
            }
            ])
            ->has('order')
            ->has('customer')
            ->get()->toJson();
    }

    /*======= Do Methods =======*/
    public function doMake(Request $request){
        $customer_id = null;

        $inventory = Inventory::find(2);
        if($inventory->quantity < $request->quantity){
            return back()
                ->withErrors(['message' => 'Stock air di gudang tidak cukup untuk melakukan order']);
        }

        if($request->new_customer){
            // If new customer //
            $this->validate($request, [
                'name' => 'required|string',
                'phone' => 'required|string|digits_between:3,14',
                'address' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'delivery_at' => 'required|date|after_or_equal:today'
            ]);

            $customer = new Customer;

            if($customer->doMake($request)){
                $customer_id = $customer->id;
            }else{
                return back()
                    ->withErrors(['message' => 'There is something wrong, please contact admin']);
            }
        }
        else{
            // If existing customer //
            $this->validate($request, [
                'customer_id' => 'required',
                'quantity' => 'required|integer|min:1',
                'delivery_at' => 'required|date|after_or_equal:today'
            ]);

            $customer_id = $request->customer_id;
        }

        $order = new Order();
        $orderCustomer = new OrderCustomer();


        if($order->doMakeOrderCustomer($request) && $orderCustomer->doMake($order, $request, $customer_id)){
            return back()
                ->with('success', 'Data telah berhasil dibuat');
        }else{
            return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doUpdate(Request $request)
    {
        $this->validate($request, [
            'outsourcing' => 'required|integer|exists:outsourcing_drivers,id',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string|regex:/^[^;]+$/'

        ]);

        $orderGallon = OrderGallon::with('outsourcingDriver','order')->find($request->id);

        //set old values
        $old_value_obj = $orderGallon->toArray();

        unset($old_value_obj['id']);
        unset($old_value_obj['outsourcing_driver_id']);
        unset($old_value_obj['order_id']);
        $old_value = '';
        $i=0;
        foreach ($old_value_obj as $row) {
            if($i == 0){
                $old_value .= $row['name'].';';
            }else if($i == 1){
                $old_value .= $row['quantity'];
            }
            $i++;
        }

        //set new values
        $new_value_obj = $request->toArray();
        $new_value_obj['outsourcing'] = OutsourcingDriver::find($new_value_obj['outsourcing'])->name;
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
            'module_name' => 'Order Gallon',
            'data_id' => $request->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description,
            'user_id' => auth()->id()
        );

        if($orderGallon->doUpdate($request) && $orderGallon->order->doUpdateOrderGallon($request) && EditHistory::create($edit_data)){
            //dd($orderGallon->id . $request->id);
            return back()
                ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doDelete(Request $request){
        $order_customer = OrderCustomer::find($request->id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $data = array(
            'module_name' => 'Order Gallon',
            'description' => $request->description,
            'data_id' => $order_customer->order_id,
            'user_id' => auth()->user()->id
        );

        if($order_customer->order->doDelete() && DeleteHistory::create($data)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }
}
