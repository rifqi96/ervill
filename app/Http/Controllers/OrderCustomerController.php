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


        if(!(new OrderCustomer())->doMake($request, $customer_id, auth()->id())){
            return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }

        return back()
            ->with('success', 'Data telah berhasil dibuat');
    }

    public function doUpdate(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'empty_gallon_quantity' => 'required|integer|min:0',
            // 'status' => 'required|in:Draft,Proses,Bermasalah,Selesai',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $order_customer = OrderCustomer::with(['customer', 'order'])->find($request->id);

        if(!$order_customer->doUpdate($request)){
            return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
        return back()
            ->with('success', 'Data telah berhasil diupdate');
    }

    public function doDelete(Request $request){
        $order_customer = OrderCustomer::find($request->id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        if($order_customer->doDelete($request->description, auth()->user()->id)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }
}
