<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderCustomerController extends OrderController
{
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        parent::setData('slug','adaw');
        dd(parent::getData());
    }
}
