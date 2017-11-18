<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderGallon;
use App\Models\OutsourcingDriver;
use App\Models\Order;
use App\Models\EditHistory;
use App\Models\DeleteHistory;

class OrderGallonController extends OrderController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'gallon';
    }

    public function index(){
        $this->data['breadcrumb'] = 'Order - Gallon Order';

        $outsourcingDrivers = OutsourcingDriver::all();
        $this->data['outsourcingDrivers'] = $outsourcingDrivers;

        return view('order.gallon.index', $this->data);
    }

    public function showMake(){
        $this->data['breadcrumb'] = 'Order - Membuat Order Gallon';

        $outsourcingDrivers = OutsourcingDriver::all();
        $this->data['outsourcingDrivers'] = $outsourcingDrivers;

        return view('order.gallon.make', $this->data);

    }

    public function doMake(Request $request)
    {
        $this->validate($request, [
            'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
            'quantity' => 'required|integer|min:1'
        ]);   

        $order = new Order();
        $orderGallon = new OrderGallon();
        
        if($order->doMakeOrderGallon($request) && $orderGallon->doMake($order, $request)){
            return back()
            ->with('success', 'Data telah berhasil dibuat');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doUpdate(Request $request)
    {        
        $this->validate($request, [           
            'outsourcing' => 'required|integer|exists:outsourcing_drivers,id',
            'quantity' => 'required|integer|min:1',
            'order_at' => 'required|date',
            'description' => 'required|string|regex:/^[^;]+$/'
            
        ]);   
        
        $orderGallon = OrderGallon::with('outsourcingDriver','order')->find($request->id);
       
        //set old values
        $old_value_obj = $orderGallon->toArray();
      
        unset($old_value_obj['id']);    
        unset($old_value_obj['outsourcing_driver_id']);
        unset($old_value_obj['order_id']);  
        $old_value = '';
        $i=0;
        foreach ($old_value_obj as $row) { 
            if($i == 0){
                $old_value .= $row['name'].';';
            }else if($i == 1){
                $old_value .= $row['quantity'].';';
                $old_value .= $row['created_at'].';';
                $old_value .= $row['accepted_at'];
            }      
            $i++;
        }

        //set new values
        $new_value_obj = $request->toArray();
        $new_value_obj['outsourcing'] = OutsourcingDriver::find($new_value_obj['outsourcing'])->name;
        unset($new_value_obj['id']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        $new_value = '';
        $i=0;
        foreach ($new_value_obj as $row) {
            if($i == count($new_value_obj)-1){
                $new_value .= $row;
            }else{
                $new_value .= $row.';';
            }
            $i++;
        }
        
        $edit_data = array(
            'module_name' => 'Order Gallon',
            'data_id' => $request->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description,
            'user_id' => auth()->id()
        );

        if($orderGallon->doUpdate($request) && EditHistory::create($edit_data)){
            return back();
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doDelete(Request $request){
        $orderGallon = OrderGallon::find($request->id);
//dd($orderGallon);
        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $data = array(
            'module_name' => 'Order Gallon',
            'description' => $request->description,
            'data_id' => $orderGallon->id,
            'user_id' => auth()->user()->id
        );

        if($orderGallon->order->doDelete() && DeleteHistory::create($data)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }

    public function showInventory(){
        $this->data['breadcrumb'] = 'Order - Inventory Gallon';

        return view('order.gallon.inventory', $this->data);

    }

    public function getOrderGallons()
    {
        $orderGallons = OrderGallon::with('outsourcingDriver','order','order.user')->get();

        $result = array();
        foreach ($orderGallons as $row) {
            if($row->order){
                array_push($result, $row);
            }
        }
        
        return json_encode($result);
    }
}
