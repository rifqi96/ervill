<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderCustomerBuy;
use App\Models\OcHeaderInvoice;
use Carbon\Carbon;

class OrderCustomerBuyController extends OrderCustomerController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['slug'] = 'customer';
    }

    /*======= Page Methods =======*/
    public function index()
    {
        $this->data['breadcrumb'] = "Customer Order - Pindah Tangan Galon";

        return view('order.customer.buy.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Customer Order - Pindah Tangan Galon - Lakukan Transaksi";
        $this->data['struks'] = (new InvoiceController())->getAllSales();

        return view('order.customer.buy.make', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAll(){
        $ocs = OrderCustomerBuy::with(['customer', 'author','orderCustomerBuyInvoices' => function($query){
            $query->with('ocHeaderInvoice');
        }])
            ->get();

        foreach($ocs as $oc){
            $oc->status = $oc->orderCustomerBuyInvoices[0]->ocHeaderInvoice->status;
        }

        return $ocs;
    }

    public function getRecentOrders(){
        $oc_buys = OrderCustomerBuy::with('customer')
            ->whereDate('buy_at', '=', Carbon::today()->toDateString())
            ->get();

        foreach($oc_buys as $oc_buy){
            $oc_buy->type = "sales";
            $oc_buy->user = $oc_buy->author;
            $oc_buy->invoice_no = $oc_buy->orderCustomerBuyInvoices[0]->ocHeaderInvoice->id;
            $oc_buy->status = $oc_buy->orderCustomerBuyInvoices[0]->ocHeaderInvoice->status;
        }

        return $oc_buys;
    }

    /*======= Do Methods =======*/
    public function doMake(Request $request){
        $this->validate($request, [
            'customer_id' => 'required|integer|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'buy_at' => 'required|date'
        ]);

        if(!(new OrderCustomerBuy())->doMake($request, auth()->id())){
            return back()
                ->withErrors(['message' => 'Input data salah, harap hubungi admin']);
        }

        return back()
            ->with('success', 'Data telah berhasil dibuat');
    }

    public function doConfirm(Request $request){
        $this->validate($request, [
            'id' => 'required|integer|exists:order_customer_buys,id'
        ]);

        $return = OrderCustomerBuy::find($request->id);

        if(!$return->doConfirm()){
            return back()
                ->withErrors(['message' => 'Gagal konfirmasi order']);
        }

        return back()
            ->with('success', 'Galon pinjam telah berhasil dibeli');
    }

    public function doCancel(Request $request){
        $this->validate($request, [
            'id' => 'required|integer|exists:order_customer_buys,id'
        ]);

        $buy = OrderCustomerBuy::find($request->id);

        if(!$buy->doCancel()){
            return back()
                ->withErrors(['message' => 'Gagal membatalkan order']);
        }

        return back()
            ->with('success', 'Order telah berhasil dibatalkan');
    }
}
