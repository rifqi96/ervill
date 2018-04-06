<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    private $data = array();
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'overview';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        abort(401, 'This action is unauthorized');
        $this->data['recent_orders'] = $this->getRecentOrders();
        $this->data['piutang_invoices'] = $this->getPiutangInvoices()['invoices'];
        $this->data['total_piutang'] = $this->getPiutangInvoices()['total'];
        $this->data['monthly_sales'] = $this->getMonthlySales()['sales'];
        $this->data['total_monthly_sales'] = $this->getMonthlySales()['total'];
        $this->data['ongoing_orders'] = $this->getOngoingOrders();
        $this->data['overdue_customers'] = $this->getOverdueCustomers();
        $this->data['charts_data'] = $this->getChartsData();
        $this->data['slug'] = "";
        $this->data['breadcrumb'] = "Home - Overview";

        return view('overview.index', $this->data);
    }

    public function getRecentOrders(){
        $recent_orders = collect();

        $ocs = (new OrderCustomerController())->getRecentOrders();
        foreach($ocs as $oc){
            $recent_orders->push($oc);
        }

        $nes = (new OrderCustomerNonErvillController())->getRecentOrders();
        foreach($nes as $ne){
            $recent_orders->push($ne);
        }

        $oc_returns = (new OrderCustomerReturnController())->getRecentOrders();
        foreach($oc_returns as $oc_return){
            $recent_orders->push($oc_return);
        }

        return $recent_orders;
    }

    public function getOngoingOrders(){
        $orders = $this->getRecentOrders();
        $res = collect();
        foreach($orders as $order){
            if($order->status == "Proses" || $order->status == "Draft"){
                $res->push($order);
            }
        }
        return $res;
    }

    public function getMonthlySales(){
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $report_ctrl = new ReportController();
        $sales = $report_ctrl->getAllByDate($startOfMonth, $endOfMonth);
        $sales['details'] = $report_ctrl->splitDetails($sales['headers']);
        $total = 0;
        foreach($sales['details'] as $detail){
            if($detail->type == "sales" && $detail->is_free != "true"){
                $total += $detail->subtotal;
            }
            else if($detail->type == "return" && $detail->payment_status == "Refund"){
                $total -= $detail->subtotal;
            }
        }

        return ['sales' => $sales['headers'], 'total' => $total];
    }

    public function getAllSales(){
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $report_ctrl = new ReportController();
        $sales = $report_ctrl->getAllByDate($startOfMonth, $endOfMonth);
        $sales['details'] = $report_ctrl->splitDetails($sales['headers']);
        $total = 0;
        foreach($sales['details'] as $detail){
            if($detail->type == "sales" && $detail->is_free != "true"){
                $total += $detail->subtotal;
            }
            else if($detail->type == "return" && $detail->payment_status == "Refund"){
                $total -= $detail->subtotal;
            }
        }

        return ['sales' => $sales['headers'], 'total' => $total];
    }

    public function getOverdueCustomers(){
        return (new Customer())->getOverdueCustomers();
    }

    public function getPiutangInvoices(){
        $invoices = (new InvoiceController())->getPiutangSales();
        $total = 0;
        foreach($invoices as $invoice){
            $total += $invoice->total;
        }

        return ['invoices' => $invoices, 'total' => $total];
    }

    public function getChartsData(){
        $chartsDatas = DB::table('oc_header_invoices')
            ->join('order_customers','oc_header_invoices.id','=','order_customers.oc_header_invoice_id')
            ->select('oc_header_invoices.delivery_at',DB::raw('SUM(er_order_customers.subtotal) as total'))
            ->where([
                ['oc_header_invoices.is_free','=','false'],
                ['oc_header_invoices.delivery_at','!=',null]
            ])
            ->groupBy('oc_header_invoices.delivery_at')
            ->get();



        $data = array();
        foreach ($chartsDatas as $chartsData) {
            $chartsData->delivery_at = Carbon::parse((Carbon::parse($chartsData->delivery_at))->toDateString())->timestamp*1000;//convert to ms timestamp
            $chartsData->total = (int) $chartsData->total;//convert string to int
            array_push($data, array_values((array) $chartsData));//only push the values of array            
        }
        $collection = collect(array_values($data));
       
        return $collection;
    }
}
