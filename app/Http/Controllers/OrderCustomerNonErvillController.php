<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerNonErvill;
use App\Models\NeHeaderInvoice;
use App\Models\OrderCustomerNonErvill;
use App\Models\Inventory;
use Carbon\Carbon;

class OrderCustomerNonErvillController extends OrderController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['slug'] = 'customerNonErvill';
    }

    /*======= Page Methods =======*/
     public function index()
    {
        $this->data['breadcrumb'] = "Order - Customer Non Ervill Order";

        $this->data['non_ervill'] = Inventory::find(6);
        //$this->data['non_aqua'] = Inventory::find(9);

        $this->data['customers'] = (new CustomerNonErvillController())->getAll();
        $this->data['invoices'] = $this->getAllInvoices();

        return view('order.customerNonErvill.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Customer Non Ervill Order - Create";

        $this->data['non_ervill'] = Inventory::find(6);
        //$this->data['non_aqua'] = Inventory::find(9);
        $this->data['customers'] = (new CustomerNonErvillController())->getAll();

        return view('order.customerNonErvill.make', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAllInvoices(){
        $invoices = NeHeaderInvoice::with(['customerNonErvill', 'orderCustomerNonErvills' => function($query){
            $query->with('priceMaster');
        }, 'user'])->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    // public function getRecentOrders(){
    //     $ocs = OcHeaderInvoice::with([
    //         'customer',
    //         'orderCustomers',
    //         'user',
    //         'shipment' => function($query){
    //             $query->with('user');
    //         }
    //         ])
    //         ->whereDate('delivery_at', '=', Carbon::today()->toDateString())
    //         ->get();

    //     foreach($ocs as $oc){
    //         $oc->type = "sales";
    //         $oc->invoice_no = $oc->id;
    //     }

    //     return $ocs;
    // }

    // public function get($id){
    //     $oc = OrderCustomer::with([            
    //         'customer',
    //         'order' => function($query){
    //             $query->with(['user', 'issues']);
    //         }
    //         ])
    //         ->has('order')
    //         ->find($id);

    //     $oc->status = $oc->orderCustomerInvoices[0]->ocHeaderInvoice->status;

    //     return $oc;
    // }

    // public function getUnshippedOrders(Request $request){
    //     $oc = OcHeaderInvoice::with(['customer', 'orderCustomers', 'user'])
    //         ->where([
    //             ['status', '=', 'Draft'],
    //             ['shipment_id', null]
    //         ])
    //         ->whereDate('delivery_at', '=', $request->delivery_at)
    //         ->get();

    //     $returns = ReHeaderInvoice::where([
    //             ['status', '=', 'Draft'],
    //             ['shipment_id', null]
    //         ])
    //         ->has('orderCustomerReturnInvoices')
    //         ->whereHas('orderCustomerReturnInvoices.orderCustomerReturn', function($query) use($request){
    //             $query->where('return_at', '=', $request->delivery_at);
    //         })
    //         ->get();

    //     $orders = collect();

    //     $oc->each(function($item, $value) use($orders){
    //         $item->type = "sales";
    //         $item->setInvoiceAttributes();
    //         $orders->push($item);
    //     });
    //     $returns->each(function($item, $value) use($orders){
    //         $item->type = "return";
    //         $item->customer = $item->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer;
    //         $orders->push($item);
    //     });

    //     return $orders;
    // }

    /*======= Do Methods =======*/
    public function doMake(Request $request){
        $customer_id = null;
        $non_ervill = Inventory::find(6);
  

        if($request->additional_price){
            $this->validate($request, [
                'additional_price' => 'required|integer'
            ]);
        }

        if(!$request->phone){
            if($request->phone != '0'){
                $request->phone = '0000';
                $request['phone'] = '0000';
            }
        }
        else if(!$request->aqua_qty || $request->aqua_qty < 1){
            if($request->aqua_qty != '0'){
                $request->aqua_qty = 0;
                $request['aqua_qty'] = 0;
            }
        }
        else if(!$request->non_aqua_qty || $request->non_aqua_qty < 1){
            if($request->non_aqua_qty != '0'){
                $request->non_aqua_qty = 0;
                $request['non_aqua_qty'] = 0;
            }
        }
        

        $total_qty = $request->aqua_qty + $request->non_aqua_qty;

        if($request->new_customer){
            // If new customer //
            if($non_ervill->quantity < $total_qty ){
                return back()
                    ->withErrors(['message' => 'Stock galon non ervill di gudang tidak cukup untuk melakukan order']);
            }
            else if($total_qty < 1){
                return back()
                    ->withErrors(['message' => 'Anda harus mengisi minimal 1 transaksi']);
            }
            $this->validate($request, [
                'name' => 'required|string',
                'phone' => 'required|string|digits_between:3,14',
                'address' => 'required|string',
                'delivery_at' => 'required|date'
            ]);
        }
        else{
            // If existing customer //          

            if($total_qty < 1){
                return back()
                    ->withErrors(['message' => 'Anda harus mengisi minimal 1 transaksi']);
            }
            else if($non_ervill->quantity < $total_qty ){
                return back()
                    ->withErrors(['message' => 'Stock air di gudang tidak cukup untuk melakukan order']);
            }
            $this->validate($request, [
                'customer_id' => 'required|integer|exists:customers,id',
                'delivery_at' => 'required|date'
            ]);
        }

        if(!(new NeHeaderInvoice())->doMake($request, auth()->id())){
            return back()
                ->withErrors(['message' => 'Input data salah, harap hubungi admin']);
        }

        return back()
            ->with('success', 'Data telah berhasil dibuat');
    }

    public function doUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|string|exists:ne_header_invoices,id',
//            'customer_id' => 'required|integer|exists:customers,id',
//            'delivery_at' => 'date',
            'description' => 'required|string|regex:/^[^;]+$/',
//            'refill_qty' => 'integer',
//            'rent_qty' => 'integer',
//            'purchase_qty' => 'integer',
//            'non_erv_qty' => 'integer',
//            'pay_qty' => 'integer'
        ]);

        if($request->additional_price){
            $this->validate($request, [
                'additional_price' => 'required|integer'
            ]);
        }

        if(!$request->aqua_qty){
            if($request->aqua_qty != '0'){
                $request->aqua_qty = 0;
                $request['aqua_qty'] = 0;
            }
        }
        else if(!$request->non_aqua_qty){
            if($request->non_aqua_qty != '0'){
                $request->non_aqua_qty = 0;
                $request['non_aqua_qty'] = 0;
            }
        }
        

        $total_qty = $request->aqua_qty + $request->non_aqua_qty;

        $non_ervill = Inventory::find(6);

        if($non_ervill->quantity < $total_qty ){
            return back()
                ->withErrors(['message' => 'Stock galon non ervill di gudang tidak cukup untuk melakukan order']);
        }
        else if($total_qty < 1){
            return back()
                ->withErrors(['message' => 'Anda harus mengisi minimal 1 transaksi']);
        }

        $invoice = NeHeaderInvoice::find($request->id);

        if(!$invoice->doUpdate($request)){
            return back()
                ->withErrors(['message' => 'Input salah, harap hubungi admin']);
        }
        return back()
            ->with('success', 'Data telah berhasil diupdate');
    }

    public function doDelete(Request $request){
        $invoice = NeHeaderInvoice::find($request->id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        if($invoice->doDelete($request, auth()->user()->id)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }

    // public function addIssueByAdmin(Request $request){
    //     $order_customer = OrderCustomer::find($request->id);

    //     $this->validate($request, [
    //         'quantity' => 'required|integer|min:1',
    //         'type' => 'required|string',
    //         'description' => 'required|string|regex:/^[^;]+$/'
    //     ]);

    //     $issue = new Issue();
    //     if($issue->doMakeIssueOrderCustomer($order_customer->order, $request)){     
    //         return back()
    //             ->with('success', 'Data telah berhasil ditambahkan masalah');             
    //     }else{
    //         return back()
    //             ->withErrors(['message' => 'Terjadi kesalahan pada penambahan masalah']);
    //     }
    // }

    public function filterBy(Request $request){
        $filters = [];
        if($request->invoice_no){
            array_push($filters, ['id', $request->invoice_no]);
        }

        if($request->customer_id){
            array_push($filters, ['customer_non_ervill_id', $request->customer_id]);
        }

        $invoices = NeHeaderInvoice::with(['customerNonErvill', 'orderCustomerNonErvills', 'user']);

        foreach($filters as $filter){
            $invoices->whereIn($filter[0], $filter[1]);
        }


        if($request->delivery_start && !$request->delivery_end){
            $invoices->where('delivery_at', '>=', $request->delivery_start);
        }
        else if($request->delivery_end && !$request->delivery_start){
            $invoices->where('delivery_at', '<=', $request->delivery_end);
        }
        else if($request->delivery_start && $request->delivery_end){
            $invoices->where([
                ['delivery_at', '>=', $request->delivery_start],
                ['delivery_at', '<=', $request->delivery_end]
            ]);
        }

        $res = $invoices->get();

        foreach($res as $re){
            $re->setInvoiceAttributes();
        }

        return $res;
    }
}
