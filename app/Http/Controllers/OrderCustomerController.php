<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OcHeaderInvoice;
use App\Models\ReHeaderInvoice;
use Illuminate\Http\Request;
use App\Models\OrderCustomer;
use App\Models\Inventory;
use App\Models\Issue;
use Carbon\Carbon;

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
        $this->data['invoices'] = $this->getAllInvoices();

        return view('order.customer.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Customer Order - Create";

        $this->data['inventory'] = Inventory::find(3);
        $this->data['customers'] = (new CustomerController())->getAll();

        return view('order.customer.make', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAllInvoices(){
        $invoices = OcHeaderInvoice::with(['customer', 'orderCustomers' => function($query){
            $query->with('priceMaster');
        }, 'shipment', 'user'])->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getRecentOrders(){
        $ocs = OcHeaderInvoice::with([
            'customer',
            'orderCustomers',
            'user',
            'shipment' => function($query){
                $query->with('user');
            }
            ])
            ->whereDate('delivery_at', '=', Carbon::today()->toDateString())
            ->get();

        foreach($ocs as $oc){
            $oc->invoice_no = $oc->id;
            $oc->setInvoiceAttributes();
        }

        return $ocs;
    }

    public function get($id){
        $oc = OrderCustomer::with([            
            'customer',
            'order' => function($query){
                $query->with(['user', 'issues']);
            }
            ])
            ->has('order')
            ->find($id);

        $oc->status = $oc->orderCustomerInvoices[0]->ocHeaderInvoice->status;

        return $oc;
    }

    public function getUnshippedOrders(Request $request){
        $oc = OcHeaderInvoice::with(['customer', 'orderCustomers', 'user'])
            ->where([
                ['status', '=', 'Draft'],
                ['shipment_id', null]
            ])
            ->whereDate('delivery_at', '=', $request->delivery_at)
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
            $item->type = "sales";
            $item->setInvoiceAttributes();
            $orders->push($item);
        });
        $returns->each(function($item, $value) use($orders){
            $item->type = "return";
            $item->customer = $item->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer;
            $orders->push($item);
        });

        return $orders;
    }

    /*======= Do Methods =======*/
    public function doMake(Request $request){
        $customer_id = null;
        $filled_gallon = Inventory::find(3);

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
        else if(!$request->rent_qty || $request->rent_qty < 1){
            if($request->rent_qty != '0'){
                $request->rent_qty = 0;
                $request['rent_qty'] = 0;
            }
        }
        else if(!$request->purchase_qty || $request->purchase_qty < 1){
            if($request->purchase_qty != '0'){
                $request->purchase_qty = 0;
                $request['purchase_qty'] = 0;
            }
        }
        else if(!$request->non_erv_qty || $request->non_erv_qty < 1){
            if($request->non_erv_qty != '0'){
                $request->non_erv_qty = 0;
                $request['non_erv_qty'] = 0;
            }
        }
        else if(!$request->pay_qty || $request->pay_qty < 1){
            if($request->pay_qty != '0'){
                $request->pay_qty = 0;
                $request['pay_qty'] = 0;
            }
        }

        $total_qty = $request->rent_qty + $request->purchase_qty + $request->non_erv_qty + $request->pay_qty;

        if($request->new_customer){
            // If new customer //
            if($filled_gallon->quantity < ($total_qty - $request->pay_qty) ){
                return back()
                    ->withErrors(['message' => 'Stock air di gudang tidak cukup untuk melakukan order']);
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
            $total_qty += $request->refill_qty;
            if(!$request->refill_qty || $request->refill_qty < 1){
                if($request->refill_qty != '0'){
                    $request->refill_qty = 0;
                    $request['refill_qty'] = 0;
                }
            }

            if($total_qty < 1){
                return back()
                    ->withErrors(['message' => 'Anda harus mengisi minimal 1 transaksi']);
            }
            else if($filled_gallon->quantity < ($total_qty - $request->pay_qty) ){
                return back()
                    ->withErrors(['message' => 'Stock air di gudang tidak cukup untuk melakukan order']);
            }
            $this->validate($request, [
                'customer_id' => 'required|integer|exists:customers,id',
                'delivery_at' => 'required|date'
            ]);
        }

        if(!(new OcHeaderInvoice())->doMake($request, auth()->id())){
            return back()
                ->withErrors(['message' => 'Input data salah, harap hubungi admin']);
        }

        return back()
            ->with('success', 'Data telah berhasil dibuat');
    }

    public function doUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|string|exists:oc_header_invoices,id',
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

        if(!$request->refill_qty){
            if($request->refill_qty != '0'){
                $request->refill_qty = 0;
                $request['refill_qty'] = 0;
            }
        }
        else if(!$request->rent_qty){
            if($request->rent_qty != '0'){
                $request->rent_qty = 0;
                $request['rent_qty'] = 0;
            }
        }
        else if(!$request->purchase_qty){
            if($request->purchase_qty != '0'){
                $request->purchase_qty = 0;
                $request['purchase_qty'] = 0;
            }
        }
        else if(!$request->non_erv_qty){
            if($request->non_erv_qty != '0'){
                $request->non_erv_qty = 0;
                $request['non_erv_qty'] = 0;
            }
        }
        else if(!$request->pay_qty || $request->pay_qty < 1){
            if($request->pay_qty != '0'){
                $request->pay_qty = 0;
                $request['pay_qty'] = 0;
            }
        }

        $total_qty = $request->refill_qty + $request->rent_qty + $request->purchase_qty + $request->non_erv_qty + $request->pay_qty;

        $filled_gallon = Inventory::find(3);

        if($filled_gallon->quantity < ($total_qty - $request->pay_qty) ){
            return back()
                ->withErrors(['message' => 'Stock air di gudang tidak cukup untuk melakukan order']);
        }
        else if($total_qty < 1){
            return back()
                ->withErrors(['message' => 'Anda harus mengisi minimal 1 transaksi']);
        }

        $invoice = OcHeaderInvoice::find($request->id);

        if(!$invoice->doUpdate($request)){
            return back()
                ->withErrors(['message' => 'Input salah, harap hubungi admin']);
        }
        return back()
            ->with('success', 'Data telah berhasil diupdate');
    }

    public function doDelete(Request $request){
        $invoice = OcHeaderInvoice::find($request->id);

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
        if($request->invoice_no){
            array_push($filters, ['id', $request->invoice_no]);
        }

        if($request->customer_id){
            array_push($filters, ['customer_id', $request->customer_id]);
        }

        $invoices = OcHeaderInvoice::with(['customer', 'orderCustomers', 'user']);

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
