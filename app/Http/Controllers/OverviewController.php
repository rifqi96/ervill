<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\OrderCustomer;
use App\Models\Customer;
use App\Models\OrderCustomerReturn;
use Illuminate\Http\Request;

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
        $this->data['recent_issues'] = $this->getRecentIssues();
        $this->data['process_orders'] = $this->getProcessOrders();
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

        $oc_buys = (new OrderCustomerBuyController())->getRecentOrders();
        foreach($oc_buys as $oc_buy){
            $recent_orders->push($oc_buy);
        }

        $oc_returns = (new OrderCustomerReturnController())->getRecentOrders();
        foreach($oc_returns as $oc_return){
            $recent_orders->push($oc_return);
        }

        return $recent_orders;
    }

    public function getProcessOrders(){
        $orders = $this->getRecentOrders();
        $res = collect();
        foreach($orders as $order){
            if($order->status == "Proses"){
                $res->push($order);
            }
        }
        return $res;
    }

    public function getRecentIssues(){
        return (new Issue())->getRecentIssues();
    }

    public function getOverdueCustomers(){
        return (new Customer())->getOverdueCustomers();
    }
}
