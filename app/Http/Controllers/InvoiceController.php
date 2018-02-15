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
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'invoice';
    }

    public function showSales()
    {
        $this->data['breadcrumb'] = "Home - Faktur - Penjualan";
        $this->data['slug'] = 'sales';
        $this->data['cash_invoices'] = $this->getCashSales();
        $this->data['piutang_invoices'] = $this->getPiutangSales();
        $this->data['free_invoices'] = $this->getFreeSales();

        return view('invoice.sales', $this->data);
    }

    public function showSalesDetails($id){
        $this->data['breadcrumb'] = "Home - Faktur - Penjualan - Detail";
        $this->data['slug'] = 'sales';
        $this->data['invoice'] = $this->getSales($id);

        return view('invoice.sales_details', $this->data);
    }

    public function showReturn()
    {
        $this->data['breadcrumb'] = "Home - Faktur - Retur";
        $this->data['slug'] = 'return';
        $this->data['refund_returns'] = $this->getRefundReturns();
        $this->data['non_refund_returns'] = $this->getNonRefundReturns();

        return view('invoice.return', $this->data);
    }

    public function showReturnDetails($id){
        $this->data['breadcrumb'] = "Home - Faktur - Retur - Detail";
        $this->data['slug'] = 'return';
        $this->data['invoice'] = $this->getReturn($id);

        return view('invoice.return_details', $this->data);
    }

    public function showSalesWHDetails($id){
        $this->data['breadcrumb'] = "Home - Faktur - Penjualan - Logistik Gudang";
        $this->data['slug'] = 'sales';
        $this->data['invoice'] = $this->getSales($id);

        return view('invoice.sales_wh_details', $this->data);
    }

    public function getAllSales($withTrashed = true){
        if($withTrashed){
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->withTrashed()
                ->get();
        }
        else{
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->get();
        }

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getCashSales($withTrashed = true){
        if($withTrashed){
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->withTrashed()
                ->where([
                    ['payment_status', '=', 'cash'],
                    ['is_free', '=', 'false']
                ])
                ->get();
        }
        else{
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->where([
                    ['payment_status', '=', 'cash'],
                    ['is_free', '=', 'false']
                ])
                ->get();
        }

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getPiutangSales($withTrashed = true){
        if($withTrashed){
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->withTrashed()
                ->where([
                    ['payment_status', '=', 'piutang'],
                    ['is_free', '=', 'false']
                ])
                ->get();
        }
        else{
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->where([
                    ['payment_status', '=', 'piutang'],
                    ['is_free', '=', 'false']
                ])
                ->get();
        }

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getFreeSales($withTrashed = true){
        if($withTrashed){
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->withTrashed()
                ->where([
                    ['is_free', '=', 'true']
                ])
                ->get();
        }
        else{
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->where([
                    ['is_free', '=', 'true']
                ])
                ->get();
        }

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getSalesByDate($start_date, $end_date, $withTrashed = true){
        if($withTrashed){
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
                ])
                ->withTrashed()
                ->where([
                    ['delivery_at', '>=', $start_date],
                    ['delivery_at', '<=', $end_date]
                ])
                ->get();
        }
        else{
            $invoices = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
                ])
                ->where([
                    ['delivery_at', '>=', $start_date],
                    ['delivery_at', '<=', $end_date]
                ])
                ->get();
        }

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getSales($id, $withTrashed = true){
        if($withTrashed){
            $invoice = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->withTrashed()
                ->find($id);
        }
        else{
            $invoice = OcHeaderInvoice::with([
                'orderCustomers', 'customer', 'user'
            ])
                ->find($id);
        }

        $invoice->setInvoiceAttributes();

        return $invoice;
    }

    public function getRefundReturns(){
        $returns = ReHeaderInvoice::with([
            'orderCustomerReturnInvoices' => function($query){
                $query->with([
                    'orderCustomerReturn' => function($query){
                        $query->with('customer');
                    },
                    'price'
                ]);
            }
        ])
            ->has('orderCustomerReturnInvoices')
            ->whereHas('orderCustomerReturnInvoices.orderCustomerReturn', function($query){
                $query->where([
                    ['is_non_refund', '=', 'false']
                ]);
            })
            ->get();

        foreach($returns as $return){
            $return->setReturnAttributes();
        }

        return $returns;
    }

    public function getNonRefundReturns(){
        $returns = ReHeaderInvoice::with([
            'orderCustomerReturnInvoices' => function($query){
                $query->with([
                    'orderCustomerReturn' => function($query){
                        $query->with('customer');
                    },
                    'price'
                ]);
            }
        ])
            ->has('orderCustomerReturnInvoices')
            ->whereHas('orderCustomerReturnInvoices.orderCustomerReturn', function($query){
                $query->where([
                    ['is_non_refund', '=', 'true']
                ]);
            })
            ->get();

        foreach($returns as $return){
            $return->setReturnAttributes();
        }

        return $returns;
    }

    public function getReturn($id){
        $return = ReHeaderInvoice::with([
            'orderCustomerReturnInvoices' => function($query){
                $query->with([
                    'orderCustomerReturn' => function($query){
                        $query->with('customer');
                    },
                    'price'
                ]);
            }
        ])
            ->has('orderCustomerReturnInvoices')
            ->find($id);

        $return->setReturnAttributes();

        return $return;
    }

    public function getFinishedReturnsByDate($start_date, $end_date){
        $returns = ReHeaderInvoice::with([
            'orderCustomerReturnInvoices' => function($query) use($start_date, $end_date){
                $query->with([
                    'orderCustomerReturn' => function($query){
                        $query->with('customer');
                    },
                    'price'
                ]);
            }
            ])
            ->has('orderCustomerReturnInvoices')
            ->whereHas('orderCustomerReturnInvoices.orderCustomerReturn', function($query) use($start_date, $end_date){
                $query->where([
                    ['status', '=', 'Selesai'],
                    ['return_at', '>=', $start_date],
                    ['return_at', '<=', $end_date]
                ]);
            })
            ->get();

        foreach($returns as $return){
            $return->setReturnAttributes();
        }

        return $returns;
    }

    public function doPay(Request $request){
        $this->validate($request, [
            'id' => 'required|string|exists:oc_header_invoices,id'
        ]);

        $oc_header_invoice = OcHeaderInvoice::find($request->id);

        if(!$oc_header_invoice->doPay()){
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan, update data gagal.']);
        }

        return back()
            ->with('success', 'Faktur berhasil dilunasi');
    }

    public function doSalesRemoveShipment($id){
        if(empty($id)){
            return back()
                ->withErrors(['No Faktur tidak boleh kosong']);
        }

        $invoice = OcHeaderInvoice::find($id);

        if(!$invoice){
            return back()
                ->withErrors(['No Faktur tidak ditemukan']);
        }

        if(!$invoice->doRemoveShipment()){
            return back()
                ->withErrors(['Terjadi kesalahan']);
        }

        return back()
            ->with('success', $id . " telah berhasil dikeluarkan dari pengiriman");
    }

    public function doReturnRemoveShipment($id){
        if(empty($id)){
            return back()
                ->withErrors(['No Faktur tidak boleh kosong']);
        }

        $invoice = ReHeaderInvoice::find($id);

        if(!$invoice){
            return back()
                ->withErrors(['No Faktur tidak ditemukan']);
        }

        if(!$invoice->doRemoveShipment()){
            return back()
                ->withErrors(['Terjadi kesalahan']);
        }

        return back()
            ->with('success', $id . " telah berhasil dikeluarkan dari pengiriman");
    }
}