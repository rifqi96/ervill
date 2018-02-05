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

    public function getAllSales(){
        $invoices = OcHeaderInvoice::with([
            'orderCustomerInvoices' => function($query){
                $query->with([
                    'orderCustomer' => function($query){
                        $query->with('customer');
                        $query->has('order');
                    }
                ]);
                $query->has('orderCustomer.order');
            },
            'orderCustomerBuyInvoices' => function($query){
                $query->with([
                    'orderCustomerBuy' => function($query){
                        $query->with('customer');
                    }
                ]);
                $query->has('orderCustomerBuy');
            }
            ])
            ->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getCashSales(){
        $invoices = OcHeaderInvoice::with([
            'orderCustomerInvoices' => function($query){
                $query->with([
                    'orderCustomer' => function($query){
                        $query->with('customer');
                        $query->has('order');
                    }
                ]);
                $query->has('orderCustomer.order');
            },
            'orderCustomerBuyInvoices' => function($query){
                $query->with([
                    'orderCustomerBuy' => function($query){
                        $query->with('customer');
                    }
                ]);
                $query->has('orderCustomerBuy');
            }
            ])
            ->where([
                ['payment_status', '=', 'cash'],
                ['is_free', '=', 'false']
            ])
            ->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getPiutangSales(){
        $invoices = OcHeaderInvoice::with([
            'orderCustomerInvoices' => function($query){
                $query->with([
                    'orderCustomer' => function($query){
                        $query->with('customer');
                        $query->has('order');
                    }
                ]);
                $query->has('orderCustomer.order');
            },
            'orderCustomerBuyInvoices' => function($query){
                $query->with([
                    'orderCustomerBuy' => function($query){
                        $query->with('customer');
                    }
                ]);
                $query->has('orderCustomerBuy');
            }
        ])
            ->where([
                ['payment_status', '=', 'piutang'],
                ['is_free', '=', 'false']
            ])
            ->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getFreeSales(){
        $invoices = OcHeaderInvoice::with([
            'orderCustomerInvoices' => function($query){
                $query->with([
                    'orderCustomer' => function($query){
                        $query->with('customer');
                        $query->has('order');
                    }
                ]);
                $query->has('orderCustomer.order');
            },
            'orderCustomerBuyInvoices' => function($query){
                $query->with([
                    'orderCustomerBuy' => function($query){
                        $query->with('customer');
                    }
                ]);
                $query->has('orderCustomerBuy');
            }
        ])
            ->where([
                ['is_free', '=', 'true']
            ])
            ->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getSalesByDate($start_date, $end_date){
        $invoices = OcHeaderInvoice::with([
                'orderCustomerInvoices' => function($query) use($start_date, $end_date){
                    $query->with([
                        'orderCustomer' => function($query){
                            $query->with('customer');
                            $query->has('order');
                        }
                    ]);
                    $query->has('orderCustomer.order');
                },
                'orderCustomerBuyInvoices' => function($query) use($start_date, $end_date){
                    $query->with([
                        'orderCustomerBuy' => function($query){
                            $query->with('customer');
                        }
                    ]);
                    $query->has('orderCustomerBuy');
                }
            ])
            ->whereHas('orderCustomerInvoices.orderCustomer', function ($query) use($start_date, $end_date){
                $query->where([
                    ['delivery_at', '>=', $start_date],
                    ['delivery_at', '<=', $end_date]
                ]);
            })
            ->orWHereHas('orderCustomerBuyInvoices.orderCustomerBuy', function ($query) use($start_date, $end_date){
                $query->where([
                    ['buy_at', '>=', $start_date],
                    ['buy_at', '<=', $end_date]
                ]);
            })
            ->get();

        foreach($invoices as $invoice){
            $invoice->setInvoiceAttributes();
        }

        return $invoices;
    }

    public function getSales($id){
        $invoice = OcHeaderInvoice::with([
            'orderCustomerInvoices' => function($query){
                $query->with([
                    'orderCustomer' => function($query){
                        $query->with(['customer', 'order']);
                        $query->has('order');
                    }
                ]);
                $query->has('orderCustomer.order');
            },
            'orderCustomerBuyInvoices' => function($query){
                $query->with([
                    'orderCustomerBuy' => function($query){
                        $query->with('customer');
                    }
                ]);
                $query->has('orderCustomerBuy');
            }
        ])
            ->find($id);

        $invoice->setInvoiceAttributes();

        return $invoice;
    }

    public function getRefundReturns(){
        $returns = ReHeaderInvoice::with([
            'orderCustomerReturnInvoices' => function($query){
                $query->with([
                    'orderCustomerReturn' => function($query){
                        $query->with('customer');
                    }
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
                    }
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
                    }
                ]);
            }
        ])
            ->has('orderCustomerReturnInvoices')
            ->find($id);

        $return->setReturnAttributes();

        return $return;
    }

    public function getReturnsByDate($start_date, $end_date){
        $returns = ReHeaderInvoice::with([
            'orderCustomerReturnInvoices' => function($query) use($start_date, $end_date){
                $query->with([
                    'orderCustomerReturn' => function($query){
                        $query->with('customer');
                    }
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
}
