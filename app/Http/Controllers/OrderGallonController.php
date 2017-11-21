<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderGallon;
use App\Models\OutsourcingDriver;
use App\Models\Order;
use App\Models\EditHistory;
use App\Models\DeleteHistory;
use App\Models\Inventory;

class OrderGallonController extends OrderController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('SuperadminAndAdmin');
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

        $orderGallon = OrderGallon::with('outsourcingDriver','order')->find($request->id);

        //check if driver_name is filled  or not
        if($orderGallon->order->accepted_at == null){
            if($request->driver_name!=null){
                return back()
                ->withErrors(['message' => 'Terjadi kesalahan, nama pengemudi tidak boleh dirubah!']);
            }
            $this->validate($request, [           
                'outsourcing' => 'required|integer|exists:outsourcing_drivers,id',
                'quantity' => 'required|integer|min:1',
                'description' => 'required|string|regex:/^[^;]+$/'                
            ]);          
        }else{
            $this->validate($request, [           
                'outsourcing' => 'required|integer|exists:outsourcing_drivers,id',
                'quantity' => 'required|integer|min:1',
                'driver_name' => 'required|string',
                'description' => 'required|string|regex:/^[^;]+$/'                
            ]);             
        } 
      
        //set old values
        $old_value_obj = $orderGallon->toArray();
     
        unset($old_value_obj['id']);    
        unset($old_value_obj['outsourcing_driver_id']);
        unset($old_value_obj['order_id']);  
        $old_value = '';
        $i=0;
        foreach ($old_value_obj as $key => $value) { 
            if($key == 'outsourcing_driver'){
                $old_value .= $value['name'].';';
            }else if($key == 'order'){
                $old_value .= $value['quantity'];
            }else{
                if($i == count($old_value_obj)-1){
                    $old_value .= $value;
                }else{
                    $old_value .= $value.';';
                }               
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

        //recalculate inventory if order is finished
        if($orderGallon->order->accepted_at != null){
            $inventory_empty_gallon = Inventory::find(1);
            $inventory_empty_gallon->add($request->quantity - $orderGallon->order->quantity);         
        }

        if($orderGallon->doUpdate($request) && $orderGallon->order->doUpdate($request) && EditHistory::create($edit_data)){
            //dd($orderGallon->id . $request->id);
            return back()
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doDelete(Request $request){
        $orderGallon = OrderGallon::find($request->id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $data = array(
            'module_name' => 'Order Gallon',
            'description' => $request->description,
            'data_id' => $orderGallon->order_id,
            'user_id' => auth()->user()->id
        );

        //recalculate inventory if order is finished
        if($orderGallon->order->accepted_at != null){
            $inventory_empty_gallon = Inventory::find(1);
            $inventory_empty_gallon->subtract($orderGallon->order->quantity);         
        }

        if($orderGallon->order->doDelete() && DeleteHistory::create($data)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }

    public function doConfirm(Request $request)
    {
        $this->validate($request, [
            'driver_name' => 'required|string'
        ]);

        $orderGallon = OrderGallon::with('order')->find($request->id);
        $inventory = Inventory::find(1);
        
        if($orderGallon->doConfirm($request->driver_name) && 
            $orderGallon->order->doConfirm() && 
            $inventory->add($orderGallon->order->quantity)){

            return back()
            ->with('success', 'Data telah berhasil dikonfirmasi');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doCancel(Request $request)
    {
        $orderGallon = OrderGallon::with('order')->find($request->id);
        $inventory = Inventory::find(1);
        
        if($orderGallon->doCancel() && 
            $orderGallon->order->doCancel() && 
            $inventory->subtract($orderGallon->order->quantity)){

            return back()
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function showInventory(){
        $this->data['breadcrumb'] = 'Order - Inventory Gallon';

        return view('order.gallon.inventory', $this->data);

    }

    public function getOrderGallons()
    {
        $orderGallons = OrderGallon::has('order')->with('outsourcingDriver','order','order.user')->get();
       
        return json_encode($orderGallons);
    }
}
