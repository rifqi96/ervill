<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;

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
}
