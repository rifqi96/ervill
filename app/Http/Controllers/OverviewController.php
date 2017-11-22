<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\OrderCustomer;
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
        $this->data['slug'] = "";
        $this->data['breadcrumb'] = "Home - Overview";

        return view('overview.index', $this->data);
    }

    public function getRecentOrders(){
        return (new OrderCustomer())->getRecentOrders();
    }

    public function getRecentIssues(){
        return (new Issue())->getRecentIssues();
    }
}
