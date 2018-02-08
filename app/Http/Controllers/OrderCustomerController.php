<?php

namespace App\Http\Controllers;

use App\Models\OcHeaderInvoice;
use App\Models\ReHeaderInvoice;
use Illuminate\Http\Request;
use App\Models\OrderCustomer;
use App\Models\Inventory;
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

        $this->data['customers'] = (new CustomerController())->getAll();
        $this->data['struks'] = (new InvoiceController())->getAllSales();
        $this->data['orders'] = $this->getAll();

        return view('order.customer.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Customer Order - Create";

        $this->data['inventory'] = Inventory::find(3);
        $this->data['customer_gallons'] = CustomerGallon::all();
        $this->data['struks'] = (new InvoiceController())->getAllSales();

        // $latest_nomor_struk_str = OrderCustomer::orderBy('nomor_struk','desc')->pluck('nomor_struk')->first();
        // $new_nomor_struk = (int)substr($latest_nomor_struk_str,2)+1;
        // $this->data['latest_nomor_struk'] = $new_nomor_struk;

        return view('order.customer.make', $this->data);
    }

//    public function showDetails($id){
//        $this->data['breadcrumb'] = "Order - Customer Order - Details";
//
//        $this->data['invoices'] = OrderCustomerInvoice::has('orderCustomer.order')->where('oc_header_invoice_id',$id)->get();
//        $this->data['buy_invoices'] = OrderCustomerBuyInvoice::where('oc_header_invoice_id',$id)->get();
//
//         return view('order.customer.details', $this->data);
//    }

    /*======= Get Methods =======*/
    public function getAll(){
        return OrderCustomer::with([
            'shipment' => function($query){
                $query->with(['user']);
            },
            'customer',
            'order' => function($query){
                $query->with(['user', 'issues']);
            },
            'orderCustomerInvoices' => function($query){
                $query->with(['ocHeaderInvoice']);
            }
            ])
            ->has('order')
            ->get();
    }

    public function get($id){
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
            ->find($id);
    }

    public function getUnshippedOrders(Request $request){
        $oc = OcHeaderInvoice::where([
                ['status', '=', 'Draft'],
                ['shipment_id', null]
            ])
            ->whereHas('orderCustomerInvoices.orderCustomer', function($query) use($request){
                $query->where('delivery_at', '=', $request->delivery_at);
            })
            ->get();
        $ocBuy = OcHeaderInvoice::where([
                ['status', '=', 'Draft'],
                ['shipment_id', null]
            ])
            ->has('orderCustomerInvoices', '<', 1)
            ->whereHas('orderCustomerBuyInvoices.orderCustomerBuy', function($query) use($request){
                $query->where('buy_at', '=', $request->delivery_at);
            })
            ->get();

        $returns = ReHeaderInvoice::where([
                ['status', '=', 'Draft'],
                ['shipment_id', null]
            ])
            ->has('orderCustomerReturnInvoices')
            ->whereHas('orderCustomerReturnInvoices.orderCustomerReturn', function($query) use($request){
                $query->where('return_at', '=', $request->delivery_at);
            })
            ->get();

        $orders = collect();

        $oc->each(function($item, $value) use($orders){
            $item->customer = $item->orderCustomerInvoices[0]->orderCustomer->customer;
            $item->type = "sales";
            $orders->push($item);
        });
        $ocBuy->each(function($item, $value) use($orders){
            $item->customer = $item->orderCustomerBuyInvoices[0]->orderCustomerBuy->customer;
            $item->type = "sales";
            $orders->push($item);
        });
        $returns->each(function($item, $value) use($orders){
            $item->customer = $item->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer;
            $item->type = "return";
            $orders->push($item);
        });

        return $orders;
    }

    /*======= Do Methods =======*/
    public function doMake(Request $request){
        $customer_id = null;
        $filled_gallon = Inventory::find(3);


        if($filled_gallon->quantity < $request->quantity){
            return back()
                ->withErrors(['message' => 'Stock air di gudang tidak cukup untuk melakukan order']);
        }

        if(!$request->phone){
            if($request->phone != '0'){
                $request->phone = '0000';
                $request['phone'] = '0000';
            }
        }

        if($request->new_customer){
            // If new customer //
            $this->validate($request, [
                'name' => 'required|string',
                'phone' => 'required|string|digits_between:3,14',
                'address' => 'required|string',
                'quantity' => 'required|integer|min:0',
                'delivery_at' => 'required|date',
                'purchase_type' => 'required'
            ]);

            // $customer = new Customer;
            // $customerGallon = new CustomerGallon;

            // //create customer
            // if($customer->doMake($request)){
            //     $customer_id = $customer->id;
            //     //create customer gallon
            //     if(!$customerGallon->doMake($request, $customer_id)){
            //         return back()
            //         ->withErrors(['message' => 'There is something wrong, please contact admin (customer telah dibuat, customergallon error)']);
            //     }              
            // }else{
            //     return back()
            //         ->withErrors(['message' => 'There is something wrong, please contact admin']);
            // }
        }
        else{
            // If existing customer //
            $this->validate($request, [
                'customer_id' => 'required|integer|exists:customers,id',
                'delivery_at' => 'required|date'
                // 'empty_quantity' => 'required|integer'
                // 'empty_non_quantity' => 'required|integer'
            ]);

            //if change nomor_struk
            if($request->change_nomor_struk){
                $this->validate($request, [               
                    'nomor_struk' => 'required'
                ]);             
            }

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

            

            //$customer_id = $request->customer_id;
        }

        // $order_customer = (new OrderCustomer())->doMake($request, auth()->id());
        // if(!$order_customer){
        //     return back()
        //         ->withErrors(['message' => 'Input data salah, harap hubungi admin']);
        // }

        // //handle nomor_struk
        // $order_customer_invoice = (new OrderCustomerInvoice())->doMake($request);
        // if(!$order_customer_invoice){
        //     return back()
        //         ->withErrors(['message' => 'Input order customer invoice data salah, harap hubungi admin']);
        // }

        if(!(new OrderCustomer())->doMake($request, auth()->id())){
            return back()
                ->withErrors(['message' => 'Input data salah, harap hubungi admin']);
        }

        return back()
            ->with('success', 'Data telah berhasil dibuat');
    }

    public function doUpdate(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer|exists:customers,id',
            'nomor_struk' => 'required',
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
                ->withErrors(['message' => 'Input salah, harap hubungi admin']);
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

    public function filterBy(Request $request){
        $filters = [];
        if($request->id){
            array_push($filters, ['id', $request->id]);
        }

        $oc = OrderCustomer::with([
            'shipment' => function($query){
                $query->with(['user']);
            },
            'customer',
            'order' => function($query){
                $query->with(['user', 'issues']);
            },
            'orderCustomerInvoices' => function($query){
                $query->with(['ocHeaderInvoice']);
            }
            ]);

        foreach($filters as $filter){
            $oc->whereIn($filter[0], $filter[1]);
        }
        if($request->nomor_struk){
            $oc->whereHas('orderCustomerInvoices', function ($query) use ($request){
                $query->whereIn('oc_header_invoice_id', $request->nomor_struk);
            });
        }

        if($request->delivery_start && !$request->delivery_end){
            $oc->where('delivery_at', '>=', $request->delivery_start);
        }
        else if($request->delivery_end && !$request->delivery_start){
            $oc->where('delivery_at', '<=', $request->delivery_end);
        }
        else if($request->delivery_start && $request->delivery_end){
            $oc->where([
                ['delivery_at', '>=', $request->delivery_start],
                ['delivery_at', '<=', $request->delivery_end]
            ]);
        }

        $oc->has('order');

        if($request->customer_name){
            $oc->whereHas('customer', function ($query) use($request){
                $query->whereIn('name', $request->customer_name);
            });
        }

        return $oc->get();
    }
}
