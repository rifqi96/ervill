<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderCustomer;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\DeleteHistory;
use App\Models\EditHistory;
use App\Models\CustomerGallon;
use App\Models\Issue;

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

        $this->data['inventory'] = Inventory::find(3);

        return view('order.customer.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Customer Order - Create";

        $this->data['inventory'] = Inventory::find(3);
        $this->data['customer_gallons'] = CustomerGallon::all();

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
        $filled_gallon = Inventory::find(3);


        if($filled_gallon->quantity < $request->quantity){
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
                'delivery_at' => 'required|date',
                'purchase_type' => 'required'
            ]);

            $customer = new Customer;
            $customerGallon = new CustomerGallon;

            //create customer
            if($customer->doMake($request)){
                $customer_id = $customer->id;
                //create customer gallon
                if(!$customerGallon->doMake($request, $customer_id)){
                    return back()
                    ->withErrors(['message' => 'There is something wrong, please contact admin (customer telah dibuat, customergallon error)']);
                }              
            }else{
                return back()
                    ->withErrors(['message' => 'There is something wrong, please contact admin']);
            }
        }
        else{
            // If existing customer //
            $this->validate($request, [
                'customer_id' => 'required|integer|exists:customers,id',
                'delivery_at' => 'required|date'
            ]);

            if($request->add_gallon){
                $this->validate($request, [
                    'quantity' => 'required|integer|min:0',
                    'add_gallon_purchase_type' => 'required|string',
                    'add_gallon_quantity' => 'required|integer|min:1'
                ]);

                if($filled_gallon->quantity < ($request->quantity + $request->add_gallon_quantity)){
                    return back()
                        ->withErrors(['message' => 'Stock air di gudang tidak cukup untuk melakukan order']);
                }
            }else{
                $this->validate($request, [                    
                    'quantity' => 'required|integer|min:1'           
                ]);
            }

            

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
            // 'status' => 'required|in:Draft,Proses,Bermasalah,Selesai',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);


        $order_customer = OrderCustomer::with(['customer', 'order'])->find($request->id);

        //new customer
        if($order_customer->is_new=='true'){
            if($order_customer->customer_id==$request->customer_id){
               $this->validate($request, [
                    'quantity' => 'required|integer|min:1',  
                    'purchase_type' => 'required|string'               
                ]); 
           }else{
                //if add more gallon
                if($request->add_gallon){
                    $this->validate($request, [
                        'quantity' => 'required|integer|min:0',
                        'add_gallon_purchase_type' => 'required|string',
                        'add_gallon_quantity' => 'required|integer|min:1'
                    ]);
                }else{
                    $this->validate($request, [                    
                        'quantity' => 'required|integer|min:1'           
                    ]);
                }
           }
            
        }else{//existing customer

            //if add more gallon
            if($request->add_gallon){
                $this->validate($request, [
                    'quantity' => 'required|integer|min:0',
                    'add_gallon_purchase_type' => 'required|string',
                    'add_gallon_quantity' => 'required|integer|min:1'
                ]);
            }else{
                $this->validate($request, [                    
                    'quantity' => 'required|integer|min:1'           
                ]);
            }
        }

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

    public function addIssueByAdmin(Request $request){
        $order_customer = OrderCustomer::find($request->id);

        $this->validate($request, [
            'quantity' => 'required|integer|min:1',
            'type' => 'required|string',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $issue = new Issue();
        if($issue->doMakeIssueOrderCustomer($order_customer->order, $request)){     
            return back()
                ->with('success', 'Data telah berhasil ditambahkan masalah');             
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penambahan masalah']);
        }
    }
}
