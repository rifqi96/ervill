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
use App\Models\Issue;

class OrderWaterController extends OrderController
{
    
    public function __construct()
    {      
    	parent::__construct();
        $this->middleware('SuperadminAndAdmin');
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
        $this->data['orderWater'] = $orderWater;       

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
            return redirect(route('order.water.index'))
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

        if($orderWater->doUpdate($request)){
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

        if($orderWater->doDelete($request->description, auth()->user()->id)){
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

        if( $orderWater->doConfirm($request->driver_name) ){
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
        
        if( $orderWater->doCancel() ){
            return back()
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doMakeIssue(Request $request){

        $orderWater = OrderWater::find($request->id);

        $inventory_empty_gallon = Inventory::find(1);
        $inventory_filled_gallon = Inventory::find(2);
        $inventory_broken_gallon = Inventory::find(3);

        // check if no type is selected
        if(!$request->typeGallon && !$request->typeSeal && !$request->typeTissue){
            return back()
            ->withErrors(['message' => 'Tipe masalah belum dipilih!']);
        }

        
        $this->validate($request, [
            'driver_name' => 'required|string'
        ]); 

        $issueGallon = new Issue();
        $issueSeal = new Issue();
        $issueTissue = new Issue();
        
        //validate request according to the types checked
        if($request->typeGallon){
            $this->validate($request, [   
                'type' => 'required',             
                'quantity_gallon' => 'required|integer|min:1|max:'.$request->max_quantity,
                'description_gallon' => 'required|string'
            ]); 
            $data = array(
                'type' => $request->type,
                'quantity' => $request->quantity_gallon,
                'description' => $request->description_gallon
            );
            $issueGallon->doMakeIssueOrderWater($request,$data);            
        }
        if($request->typeSeal){
            $this->validate($request, [               
                'quantity_seal' => 'required|integer|min:1',
                'description_seal' => 'required|string'
            ]);
            $data = array(
                'quantity' => $request->quantity_seal,
                'description' => $request->description_seal
            );
            $issueSeal->doMakeIssueOrderWater($request,$data);     
        }
        if($request->typeTissue){
            $this->validate($request, [                
                'quantity_tissue' => 'required|integer|min:1',
                'description_tissue' => 'required|string'
            ]);
            $data = array(
                'quantity' => $request->quantity_tissue,
                'description' => $request->description_tissue
            );
            $issueTissue->doMakeIssueOrderWater($request,$data);  
        }


        //set quantity for recalculating inventory
        $empty_gallon_quantity = $orderWater->order->quantity;
        $filled_gallon_quantity = $orderWater->order->quantity;
        $broken_gallon_quantity = 0;

        if($request->typeGallon){
            $issueGallon->save();                    
            $filled_gallon_quantity -= $issueGallon->quantity;
            $broken_gallon_quantity = $issueGallon->quantity;
        }
        if($request->typeSeal){
            $issueSeal->save();
        }
        if($request->typeTissue){
            $issueTissue->save();    
        }

        if( $orderWater->doConfirmWithIssue($request->driver_name) && 
            $orderWater->order->doConfirm() && 
            $inventory_empty_gallon->subtract($empty_gallon_quantity) && 
            $inventory_filled_gallon->add($filled_gallon_quantity) &&
            $inventory_broken_gallon->add($broken_gallon_quantity)){

            return redirect(route('order.water.index'))
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
        
    }

    public function getAll()
    {
        $orderWaters = OrderWater::has('order')->with('outsourcingWater','outsourcingDriver','order','order.user','order.issues')->get();
    
        return json_encode($orderWaters);
    }
}
