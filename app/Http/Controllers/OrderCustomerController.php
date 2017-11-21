<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderCustomer;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\DeleteHistory;
use App\Models\EditHistory;

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

        $this->data['inventory'] = Inventory::find(2);

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
            ->get();
    }

    public function getUnshippedOrders(Request $request){
        return OrderCustomer::with([
            'shipment' => function($query){
                $query->with(['user']);
            },
            'customer',
            'order' => function($query){
                $query->with(['user', 'issues']);
            }
            ])
            ->where([
                ['delivery_at','=',$request->delivery_at],
                ['status','=','draft'],
                ['shipment_id', null]
            ])
            ->whereHas('order', function ($query){
                $query->where('accepted_at', null);
            })
            ->has('customer')
            ->get();
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
                'customer_id' => 'required|integer|exists:customers,id',
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
            'customer_id' => 'required|integer|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'empty_gallon_quantity' => 'required|integer|min:0',
            'status' => 'required|in:Draft,Proses,Bermasalah,Selesai',
            'description' => 'required|string|regex:/^[^;]+$/'

        ]);

        $order_customer = OrderCustomer::with('customer', 'order')->find($request->id);

        //set old values
        $old_value_obj = $order_customer->toArray();

        $old_value_obj['customer_name'] = $old_value_obj['customer']['name'];
        $old_value_obj['quantity'] = $old_value_obj['order']['quantity'];

        unset($old_value_obj['id']);
        unset($old_value_obj['shipment_id']);
        unset($old_value_obj['customer_id']);
        unset($old_value_obj['order_id']);
        unset($old_value_obj['order']);
        unset($old_value_obj['customer']);

        $old_value = '';
        $old_value .= $old_value_obj['quantity'] . ';';
        $old_value .= $old_value_obj['empty_gallon_quantity']. ';';
        $old_value .= $old_value_obj['delivery_at']. ';';
        $old_value .= $old_value_obj['customer_name']. ';';
        $old_value .= $old_value_obj['status'];

        //set new values
        $new_value_obj = $request->toArray();
        $new_customer = Customer::find($request->customer_id);
        $new_value_obj['customer_name'] = $new_customer->name;
        unset($new_value_obj['id']);
        unset($new_value_obj['customer-table_length']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        $new_value = '';
        $new_value .= $new_value_obj['quantity'] . ';';
        $new_value .= $new_value_obj['empty_gallon_quantity']. ';';
        $new_value .= $new_value_obj['delivery_at']. ';';
        $new_value .= $new_value_obj['customer_name']. ';';
        $new_value .= $new_value_obj['status'];

        $edit_data = array(
            'module_name' => 'Order Customer',
            'data_id' => $request->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description,
            'user_id' => auth()->id()
        );

        if($order_customer->doUpdate($request) && EditHistory::create($edit_data)){
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
            'module_name' => 'Order Customer',
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
