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
        $this->data['module'] = 'order';
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
        $invoices = NeHeaderInvoice::with(['customer', 'orderCustomerNonErvills' => function($query){
            $query->with('priceMaster');
        }, 'user'])->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getRecentOrders(){
        $nes = NeHeaderInvoice::with([
            'orderCustomerNonErvills', 'customer', 'user'
        ])
            ->whereDate('delivery_at', '=', Carbon::today()->toDateString())
            ->get();

        foreach($nes as $ne){
            $ne->invoice_no = $ne->id;
            $ne->setInvoiceAttributes();
        }

        return $nes;
    }

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
            'description' => 'required|string|regex:/^[^;]+$/'
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
        $current_quantity = 0;

        $invoice = NeHeaderInvoice::find($request->id);

        foreach ($invoice->orderCustomerNonErvills as $orderCustomerNonErvill) {
            $current_quantity += $orderCustomerNonErvill->quantity;
        }

        if( ($non_ervill->quantity + $current_quantity) < $total_qty ){
            return back()
                ->withErrors(['message' => 'Stock galon non ervill di gudang tidak cukup untuk melakukan order']);
        }
        else if($total_qty < 1){
            return back()
                ->withErrors(['message' => 'Anda harus mengisi minimal 1 transaksi']);
        }
        
        

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

    public function doConfirm(Request $request)
    {        

        $invoice = NeHeaderInvoice::find($request->id);

        if( $invoice->doConfirm($request) ){
            return back()
            ->with('success', 'Data telah berhasil dikonfirmasi');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doCancel(Request $request)
    {        

        $invoice = NeHeaderInvoice::find($request->id);

        if( $invoice->doCancel($request) ){
            return back()
            ->with('success', 'Data telah berhasil dibatalkan');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function filterBy(Request $request){
        $filters = [];
        if($request->invoice_no){
            array_push($filters, ['id', $request->invoice_no]);
        }

        if($request->customer_id){
            array_push($filters, ['customer_non_ervill_id', $request->customer_id]);
        }

        $invoices = NeHeaderInvoice::with(['customer', 'orderCustomerNonErvills', 'user']);

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
