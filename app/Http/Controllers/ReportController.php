<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct(){
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'report';
    }

    public function showSales()
    {
        $this->data['breadcrumb'] = "Home - Laporan - Penjualan";
        $this->data['slug'] = 'sales';

        $this->data['customers'] = (new CustomerController())->getAll();
        $this->data['struks'] = (new InvoiceController())->getAllSales();

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->data['report'] = $this->getAllByDate($startOfMonth, $endOfMonth);
        $this->data['report']['details'] = $this->splitDetails($this->data['report']['headers']);

        return view('report.sales', $this->data);
    }

    public function getAllByDate($start_date, $end_date){
        $res = collect([
            'headers' => collect(),
            'params' => collect([
                'start_date' => $start_date,
                'end_date' => $end_date
            ])
        ]);

        $invoice = new InvoiceController();
        $all_sales = $invoice->getSalesByDate($start_date, $end_date);
        $all_sales->each(function ($item, $key) use($res){
            $item->type = "sales";
            $res['headers']->push($item);
        });

        $all_returns = $invoice->getReturnsByDate($start_date, $end_date);
        $all_returns->each(function ($item, $key) use($res){
            $item->type = "return";
            $res['headers']->push($item);
        });
        $i=1;
        foreach($res['headers']->sortBy('delivery_at') as $row){
            $row->no = $i;
            $i++;
        }

        return $res;
    }

    public function splitDetails($data){
        $res = collect();

        foreach($data as $header){
            if($header->has_order){
                if($header->type == "sales" && $header->is_only_buy){
                    foreach($header->orderCustomerBuyInvoices as $ocBuyInvoice){
                        $res->push($ocBuyInvoice);
                    }
                }
                else if($header->type == "sales" && !$header->is_only_buy){
                    foreach($header->orderCustomerInvoices as $ocInvoice){
                        $res->push($ocInvoice);
                    }
                    foreach($header->orderCustomerBuyInvoices as $ocBuyInvoice){
                        $res->push($ocBuyInvoice);
                    }
                }
                else if($header->type == "return"){
                    foreach($header->orderCustomerReturnInvoices as $ocReturnInvoice){
                        $res->push($ocReturnInvoice);
                    }
                }
            }
        };

        return $res;
    }

    public function filterBy(Request $request){
        $this->validate($request, [
            'start_date' => 'required|before_or_equal:end_date',
            'end_date' => 'required|after_or_equal:start_date',
            'type' => 'required|in:all,lunas,piutang,free,return'
        ]);
    }
}
