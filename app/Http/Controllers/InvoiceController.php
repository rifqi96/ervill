<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\OcHeaderInvoice;
use App\Models\OrderCustomerInvoice;
use App\Models\OrderCustomerBuyInvoice;

use App\Models\ReHeaderInvoice;
use App\Models\OrderCustomerReturnInvoice;

class InvoiceController extends Controller
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'invoice';
    }

    public function showSales()
    {
        $this->data['breadcrumb'] = "Home - Faktur - Penjualan";
        $this->data['slug'] = 'sales';

        return view('invoice.sales', $this->data);
    }

    public function showReturn()
    {
        $this->data['breadcrumb'] = "Home - Faktur - Retur";
        $this->data['slug'] = 'return';

        return view('invoice.return', $this->data);
    }

    public function getAllSales(){
        return OcHeaderInvoice::with([
            'orderCustomerInvoices' => function($query){
                $query->with([
                    'orderCustomer' => function($query){
                    }
                ]);
            },
            'orderCustomerBuyInvoices' => function($query){
                $query->with('orderCustomerBuy');
            }
        ]);
    }
}
