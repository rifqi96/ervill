<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderCustomerController extends OrderController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'customer';
    }

     public function index()
    {
        $this->data['breadcrumb'] = "Order - Customer Order";

        return view('order.customer.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Customer Order - Create";

        return view('order.customer.make', $this->data);
    }

}
