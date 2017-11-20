<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderWater;
use App\Models\OutsourcingWater;
use App\Models\OutsourcingDriver;
use App\Models\Inventory;
use App\Models\Order;

class OrderWaterController extends OrderController
{
    
    public function __construct()
    {      
    	parent::__construct();
        $this->data['slug'] = "water";
    }

    public function index()
    {
        $this->data['breadcrumb'] = "Order - Water Order";

        return view('order.water.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Water Order - Create";

        $outsourcingWaters = OutsourcingWater::all();
        $outsourcingDrivers = OutsourcingDriver::all();
        $max_quantity = $this->countMaxQuantity();
        $this->data['outsourcingDrivers'] = $outsourcingDrivers;
        $this->data['outsourcingWaters'] = $outsourcingWaters;
        $this->data['max_quantity'] = $max_quantity;


        return view('order.water.make', $this->data);
    }

    public function countMaxQuantity(){
        $inventory_quantity = Inventory::find(1)->quantity;
        $orderWatersActive = OrderWater::whereHas('order',function($query){
            $query->where('accepted_at',null);
        })->get();

        $orderWater_quantity = 0;
        foreach ($orderWatersActive as $row) {
            $orderWater_quantity += $row->order->quantity;
        }

        $max_quantity = $inventory_quantity - $orderWater_quantity;
        return $max_quantity;
    }

    public function createIssue(OrderWater $orderWater){
    	$this->data['breadcrumb'] = "Order - Water Order - Issue";

        return view('order.water.issue', $this->data);
    }

    public function doMake(Request $request)
    {
        $this->validate($request, [
            'outsourcing_water' => 'required|integer|exists:outsourcing_waters,id',
            'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
            'quantity' => 'required|integer|min:1',
            'delivery_at' => 'required|date|after_or_equal:today'
        ]);           

        $order = new Order();
        $orderWater = new OrderWater();
        
        if($order->doMakeOrderWater($request) && $orderWater->doMake($order, $request)){
            return back()
            ->with('success', 'Data telah berhasil dibuat');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function getAll()
    {
        $orderWaters = OrderWater::has('order')->with('outsourcingWater','outsourcingDriver','order','order.user')->get();
       
        return json_encode($orderWaters);
    }
}
