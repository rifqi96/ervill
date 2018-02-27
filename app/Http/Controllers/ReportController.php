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
        $this->data['struks'] = (new InvoiceController())->getAllSales(false);

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
        $all_sales = $invoice->getSalesByDate($start_date, $end_date, false);
        $all_sales->each(function ($item, $key) use($res){
            if($item->status == "Selesai"){
                $res['headers']->push($item);
            }
        });

        $all_returns = $invoice->getFinishedReturnsByDate($start_date, $end_date);
        $all_returns->each(function ($item, $key) use($res){
            $res['headers']->push($item);
        });

        $i=1;
        foreach($res['headers']->sortBy('delivery_at')->sortBy('id') as $row){
            $row->no = $i;
            $i++;
        }

        return $res;
    }

    public function splitDetails($data){
        $res = collect();

        foreach($data as $header){
            if($header->type == "sales"){
                if($header->invoice_code == "oc"){
                    foreach($header->orderCustomers as $order){
                        $res->push($order);
                        $res[$res->count()-1]->delivery_at = $header->delivery_at;
                        $res[$res->count()-1]->type = $header->type;
                        $res[$res->count()-1]->payment_status = $header->payment_status;
                        $res[$res->count()-1]->oc_header_invoice_id = $header->id;
                        $res[$res->count()-1]->customer = $header->customer;
                        $res[$res->count()-1]->is_free = $header->is_free;
                        $res[$res->count()-1]->description = $header->description;
                        $res[$res->count()-1]->invoice_code = $header->invoice_code;
                    }
                }
                else if($header->invoice_code == "ne"){
                    foreach($header->orderCustomerNonErvills as $order){
                        $res->push($order);
                        $res[$res->count()-1]->delivery_at = $header->delivery_at;
                        $res[$res->count()-1]->type = $header->type;
                        $res[$res->count()-1]->payment_status = $header->payment_status;
                        $res[$res->count()-1]->ne_header_invoice_id = $header->id;
                        $res[$res->count()-1]->customer = $header->customer;
                        $res[$res->count()-1]->is_free = $header->is_free;
                        $res[$res->count()-1]->description = $header->description;
                        $res[$res->count()-1]->invoice_code = $header->invoice_code;
                    }
                }
            }
            else{
                foreach($header->orderCustomerReturnInvoices as $ocReturnInvoice){
                    $res->push($ocReturnInvoice);
                    $res[$res->count()-1]->delivery_at = $header->delivery_at;
                    $res[$res->count()-1]->type = $header->type;
                    $res[$res->count()-1]->payment_status = $header->payment_status;
                    $res[$res->count()-1]->invoice_code = $header->invoice_code;
                }
            }
        };

        $i = 1;
        foreach($res->sortBy('delivery_at') as $row){
            $row->no = $i;
            $i++;
        }

        return $res;
    }

    public function filterBy(Request $request){
        $this->validate($request, [
            'start_date' => 'required|before_or_equal:end_date',
            'end_date' => 'required|after_or_equal:start_date',
            'type' => 'required'
        ]);

        if($request->type[0] == "all"){
            $report = $this->getAllByDate($request->start_date, $request->end_date);
        }
        else{
            $report = $this->getAllByDate($request->start_date, $request->end_date);

            $sales = $report['headers']->filter(function ($value, $key){
                return $value->type == "sales";
            });
            $returns = $report['headers']->filter(function ($value, $key){
                return $value->type == "return";
            });

            $paids = collect();
            $piutangs = collect();
            $frees = collect();

            foreach($sales as $sale){
                // Paid
                if($sale->payment_status == "cash" && $sale->is_free == "false"){
                    $paids->push($sale);
                }
                // Piutang
                if($sale->payment_status == "piutang" && $sale->is_free == "false"){
                    $piutangs->push($sale);
                }
                // Free
                if($sale->is_free == "true"){
                    $frees->push($sale);
                }
            }

            $report['headers'] = collect();
            foreach($request->type as $type){
                if($type == "lunas"){
                    $paids->each(function ($item, $key) use ($report){
                        $report['headers']->push($item);
                    });
                }
                else if($type == "piutang"){
                    $piutangs->each(function ($item, $key) use ($report){
                        $report['headers']->push($item);
                    });
                }
                else if($type == "free"){
                    $frees->each(function ($item, $key) use ($report){
                        $report['headers']->push($item);
                    });
                }
                else if($type == "return"){
                    $returns->each(function ($item, $key) use ($report){
                        $report['headers']->push($item);
                    });
                }
            }
        }

        $details = $this->splitDetails($report['headers']);

        return $details;
    }
}
