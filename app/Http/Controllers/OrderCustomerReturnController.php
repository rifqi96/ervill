<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderCustomerReturn;
use Carbon\Carbon;

class OrderCustomerReturnController extends Controller
{
    public function __construct(){
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'return';
        $this->data['slug'] = '';
    }

    /*======= Page Methods =======*/
    public function index()
    {
        $this->data['breadcrumb'] = "Customer Order - Retur";

        return view('return.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Customer Order - Retur - Create";

        $this->data['customers'] = (new CustomerController())->getAll();

        return view('return.make', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAll(){
        $oc_returns = OrderCustomerReturn::with(['customer', 'author','orderCustomerReturnInvoices','orderCustomerReturnInvoices.reHeaderInvoice'])
            ->get();

        foreach($oc_returns as $oc_return){
            $oc_return->type = "return";
            $oc_return->user = $oc_return->author;
            $oc_return->invoice_no = $oc_return->orderCustomerReturnInvoices[0]->reHeaderInvoice->id;
            $oc_return->status = $oc_return->orderCustomerReturnInvoices[0]->reHeaderInvoice->status;
        }

        return $oc_returns;
    }

    public function getRecentOrders(){
        $oc_returns = OrderCustomerReturn::with(['customer', 'author','orderCustomerReturnInvoices','orderCustomerReturnInvoices.reHeaderInvoice', 'orderCustomerReturnInvoices.reHeaderInvoice.shipment', 'orderCustomerReturnInvoices.reHeaderInvoice.shipment.user'])
            ->whereDate('return_at', '=', Carbon::today()->toDateString())
            ->get();
        foreach($oc_returns as $oc_return){
            $oc_return->type = "return";
            $oc_return->user = $oc_return->author;
            $oc_return->invoice_no = $oc_return->orderCustomerReturnInvoices[0]->reHeaderInvoice->id;
            $oc_return->status = $oc_return->orderCustomerReturnInvoices[0]->reHeaderInvoice->status;
            $oc_return->shipment = $oc_return->orderCustomerReturnInvoices[0]->reHeaderInvoice->shipment;
        }

        return $oc_returns;
    }

    /*======= Do Methods =======*/
    public function doMake(Request $request){
        $this->validate($request, [
            'customer_id' => 'required|integer|exists:customers,id',
            'return_at' => 'required|date',
            'description' => 'required'
        ]);

        if(!$request->empty_quantity || $request->empty_quantity == null){
            $request->empty_quantity = 0;
            $request['empty_quantity'] = 0;
        }

        if(!$request->filled_quantity || $request->filled_quantity == null){
            $request->filled_quantity = 0;
            $request['filled_quantity'] = 0;
        }

        if($request->empty_quantity == 0 && $request->filled_quantity == 0){
            return back()
                ->withErrors(['message' => 'Input quantity tidak boleh kosong']);
        }

        if(!(new OrderCustomerReturn)->doMake($request, auth()->id())){
            return back()
                ->withErrors(['message' => 'Input data salah, harap hubungi admin']);
        }

        return back()
            ->with('success', 'Data telah berhasil dibuat');
    }

    public function doConfirm(Request $request){
        $this->validate($request, [
            'id' => 'required|integer|exists:order_customer_returns,id'
        ]);

        $return = OrderCustomerReturn::find($request->id);

        if(!$return->doConfirm()){
            return back()
                ->withErrors(['message' => 'Gagal konfirmasi retur']);
        }

        return back()
            ->with('success', 'Galon telah berhasil diretur ke gudang');
    }

    public function doCancel(Request $request){
        $this->validate($request, [
            'id' => 'required|integer|exists:order_customer_returns,id'
        ]);

        $return = OrderCustomerReturn::find($request->id);

        if(!$return->doCancel()){
            return back()
                ->withErrors(['message' => 'Gagal batalkan retur']);
        }

        return back()
            ->with('success', 'Galon telah berhasil dikembalikan ke customer');
    }
}
