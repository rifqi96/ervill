<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderCustomerBuy;
use App\Models\OcHeaderInvoice;

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
        $this->data['struks'] = $this->getNomorStruk();

        return view('order.customer.buy.make', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAll(){
        return OrderCustomerBuy::with(['customer', 'author','orderCustomerBuyInvoices'])
            ->get();
    }
    public function getNomorStruk(){
        return OcHeaderInvoice::has('orderCustomerInvoices')->pluck('id');

       
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

    public function doDelete(Request $request){
        $this->validate($request, [
            'id' => 'required|integer|exists:order_customer_buys,id'
        ]);

        $buy = OrderCustomerBuy::find($request->id);

        if(!$buy->doDelete()){
            return back()
                ->withErrors(['message' => 'Gagal menghapus data']);
        }

        return back()
            ->with('success', 'Data telah berhasil dihapus');
    }
}
