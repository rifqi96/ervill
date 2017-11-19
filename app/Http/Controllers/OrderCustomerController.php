<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderCustomer;

class OrderCustomerController extends OrderController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'customer';
    }

    /*======= Page Methods =======*/
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

    /*======= Get Methods =======*/

    /*======= Do Methods =======*/
    public function doMake(Request $request){
        $this->validate($request, [
            'customer_name' => 'required|string',
            'customer_address' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'delivery_at' => 'required|date|after_or_equal:today'
        ]);

        $order = new Order();
        $orderCustomer = new OrderCustomer();

        if($order->doMakeOrderCustomer($request) && $orderCustomer->doMake($order, $request)){
            return back()
                ->with('success', 'Data telah berhasil dibuat');
        }else{
            return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }
}
