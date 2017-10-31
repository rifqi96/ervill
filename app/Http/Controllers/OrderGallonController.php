<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderGallonController extends OrderController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'gallon';
    }

    public function index(){
        $this->data['breadcrumb'] = 'Order - Gallon Order';

        return view('order.gallon.index', $this->data);
    }

    public function showMake(){
        $this->data['breadcrumb'] = 'Order - Membuat Order Gallon';

        return view('order.gallon.make', $this->data);

    }

    public function showInventory(){
        $this->data['breadcrumb'] = 'Order - Inventory Gallon';

        return view('order.gallon.inventory', $this->data);

    }
}
