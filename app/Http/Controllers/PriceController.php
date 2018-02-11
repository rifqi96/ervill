<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;

class PriceController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['slug'] = 'price';
    }

    /*======= Page Methods =======*/
    public function index()
    {
        $this->data['breadcrumb'] = "Home - Daftar Harga";

        $this->data['price_list'] = $this->getAll();
        $this->data['customer_sale_prices'] = $this->getEndCustomerSalePrices();
        $this->data['customer_return_prices'] = $this->getEndCustomerReturnPrices();
        $this->data['agent_sale_prices'] = $this->getAgentSalePrices();
        $this->data['agent_return_prices'] = $this->getAgentReturnPrices();

        return view('price.index', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAll(){
        return Price::all();
    }

    public function getEndCustomerSalePrices(){
        return Price::where([
            ['customer_type', '=', 'end_customer']
        ])
            ->whereNotIn('id', [6,7])
            ->get();
    }

    public function getEndCustomerReturnPrices(){
        return Price::whereIn('id', [6,7])
            ->get();
    }

    public function getAgentSalePrices(){
        return Price::where([
            ['customer_type', '=', 'agent']
        ])
            ->whereNotIn('id', [13,14])
            ->get();
    }

    public function getAgentReturnPrices(){
        return Price::whereIn('id', [13,14])
            ->get();
    }

    /*======= Do Methods =======*/
    public function doUpdate(Request $request){
        $this->validate($request, [
            'id' => 'required',
            'price' => 'required|integer',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $price = Price::find($request->id);

        if(!$price){
            return back()
                ->withErrors(['message' => 'Data tidak ditemukan']);
        }

        if(!$price->doUpdate($request)){
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada input data']);
        }

        return back()
            ->with('success', 'Data telah berhasil diupdate');
    }
}
