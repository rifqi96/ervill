<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderWater;
use App\Models\OutsourcingWater;
use App\Models\OutsourcingDriver;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\EditHistory;
use App\Models\DeleteHistory;

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

        $outsourcingWaters = OutsourcingWater::all();
        $outsourcingDrivers = OutsourcingDriver::all();
        $max_quantity = $this->countMaxQuantity();
        $this->data['outsourcingDrivers'] = $outsourcingDrivers;
        $this->data['outsourcingWaters'] = $outsourcingWaters;
        $this->data['max_quantity'] = $max_quantity;

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
        $max_quantity = $this->countMaxQuantity();
        $this->validate($request, [
            'outsourcing_water' => 'required|integer|exists:outsourcing_waters,id',
            'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
            'quantity' => 'required|integer|min:1|max:'.$max_quantity,
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

    public function doUpdate(Request $request)
    {        
        $orderWater = OrderWater::with('outsourcingWater','outsourcingDriver','order')->find($request->id);

        //check if driver_name is filled  or not
        if($orderWater->order->accepted_at == null){
            if($request->driver_name!=null){
                return back()
                ->withErrors(['message' => 'Terjadi kesalahan, nama pengemudi tidak boleh dirubah!']);
            }
            $this->validate($request, [           
                'outsourcing_water' => 'required|integer|exists:outsourcing_waters,id',
                'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
                'quantity' => 'required|integer|min:1|max:'.$request->max_quantity,
                'delivery_at' => 'required|date',
                'description' => 'required|string|regex:/^[^;]+$/'
                
            ]);          
        }else{
            $this->validate($request, [           
                'outsourcing_water' => 'required|integer|exists:outsourcing_waters,id',
                'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
                'driver_name' => 'required|string',
                'quantity' => 'required|integer|min:1|max:'.$request->max_quantity,
                'delivery_at' => 'required|date',
                'description' => 'required|string|regex:/^[^;]+$/'
                
            ]);
        } 
        
        //set old values
        $old_value_obj = $orderWater->toArray();
        unset($old_value_obj['id']);    
        unset($old_value_obj['outsourcing_water_id']);
        unset($old_value_obj['outsourcing_driver_id']);
        unset($old_value_obj['order_id']);  
        unset($old_value_obj['status']);  
        $old_value = '';
        $i=0;

        foreach ($old_value_obj as $key => $value) { 
            if($key == 'outsourcing_water' || $key == 'outsourcing_driver'){
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
        $new_value_obj['outsourcing_water'] = OutsourcingWater::find($new_value_obj['outsourcing_water'])->name;
        $new_value_obj['outsourcing_driver'] = OutsourcingDriver::find($new_value_obj['outsourcing_driver'])->name;
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
            'module_name' => 'Order Water',
            'data_id' => $request->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description,
            'user_id' => auth()->id()
        );

        //recalculate inventory if order is finished
        if($orderWater->order->accepted_at != null){
            $inventory_empty_gallon = Inventory::find(1);
            $inventory_filled_gallon = Inventory::find(2);
            $inventory_empty_gallon->subtract($request->quantity - $orderWater->order->quantity);
            $inventory_filled_gallon->add($request->quantity - $orderWater->order->quantity);
        }

        if($orderWater->doUpdate($request) && $orderWater->order->doUpdate($request) && EditHistory::create($edit_data)){
            //dd($orderWater->id . $request->id);
            return back()
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doDelete(Request $request){
        $orderWater = OrderWater::find($request->id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $data = array(
            'module_name' => 'Order Water',
            'description' => $request->description,
            'data_id' => $orderWater->order_id,
            'user_id' => auth()->user()->id
        );

        //recalculate inventory if order is finished
        if($orderWater->order->accepted_at != null){
            $inventory_empty_gallon = Inventory::find(1);
            $inventory_filled_gallon = Inventory::find(2);
            $inventory_empty_gallon->add($orderWater->order->quantity);
            $inventory_filled_gallon->subtract($orderWater->order->quantity);
        }
        

        if($orderWater->order->doDelete() && DeleteHistory::create($data)){
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

        $orderWater = OrderWater::with('order')->find($request->id);
        $inventory_empty_gallon = Inventory::find(1);
        $inventory_filled_gallon = Inventory::find(2);
        
        if( $orderWater->doConfirm($request->driver_name) && 
            $orderWater->order->doConfirm() && 
            $inventory_empty_gallon->subtract($orderWater->order->quantity) && 
            $inventory_filled_gallon->add($orderWater->order->quantity)){

            return back()
            ->with('success', 'Data telah berhasil dikonfirmasi');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doCancel(Request $request)
    {
        $orderWater = OrderWater::with('order')->find($request->id);
        $inventory_empty_gallon = Inventory::find(1);
        $inventory_filled_gallon = Inventory::find(2);
        
        if( $orderWater->doCancel() && 
            $orderWater->order->doCancel() && 
            $inventory_empty_gallon->add($orderWater->order->quantity) && 
            $inventory_filled_gallon->subtract($orderWater->order->quantity)){

            return back()
            ->with('success', 'Data telah berhasil diupdate');
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
