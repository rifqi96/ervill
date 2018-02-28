<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderWater;
//use App\Models\OutsourcingWater;
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

//        $outsourcingWaters = OutsourcingWater::all();
        $outsourcingDrivers = OutsourcingDriver::all();
        $max_buffer_qty = $this->countMaxBufferQuantity();
        $max_warehouse_qty = $this->countMaxWarehouseQuantity();
        $this->data['outsourcingDrivers'] = $outsourcingDrivers;
//        $this->data['outsourcingWaters'] = $outsourcingWaters;
        $this->data['max_buffer_qty'] = $max_buffer_qty;
        $this->data['max_warehouse_qty'] = $max_warehouse_qty;

        return view('order.water.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Order - Water Order - Create";

//        $outsourcingWaters = OutsourcingWater::all();
        $outsourcingDrivers = OutsourcingDriver::all();
        $max_buffer_qty = $this->countMaxBufferQuantity();
        $max_warehouse_qty = $this->countMaxWarehouseQuantity();
        $this->data['outsourcingDrivers'] = $outsourcingDrivers;
//        $this->data['outsourcingWaters'] = $outsourcingWaters;
        $this->data['max_buffer_qty'] = $max_buffer_qty;
        $this->data['max_warehouse_qty'] = $max_warehouse_qty;


        return view('order.water.make', $this->data);
    }

    public function countMaxBufferQuantity(){
        $inventory_quantity = Inventory::find(1)->quantity;
        $orderWatersActive = OrderWater::whereHas('order',function($query){
            $query->where('accepted_at',null);
        })->get();

        $orderWater_quantity = 0;
        foreach ($orderWatersActive as $row) {
            $orderWater_quantity += $row->buffer_qty;
        }

        $max_quantity = $inventory_quantity - $orderWater_quantity;
        return $max_quantity;
    }

    public function countMaxWarehouseQuantity(){
        $inventory_quantity = Inventory::find(2)->quantity;
        $orderWatersActive = OrderWater::whereHas('order',function($query){
            $query->where('accepted_at',null);
        })->get();

        $orderWater_quantity = 0;
        foreach ($orderWatersActive as $row) {
            $orderWater_quantity += $row->warehouse_qty;
        }

        $max_quantity = $inventory_quantity - $orderWater_quantity;
        return $max_quantity;
    }

    public function createIssue(OrderWater $orderWater){
    	$this->data['breadcrumb'] = "Order - Water Order - Issue";
        $this->data['orderWater'] = $orderWater;     
        if($orderWater->status!="proses"){
           return back()
                ->withErrors(['message' => 'Order ini tidak bisa dibuat masalah']); 
        }  

        return view('order.water.issue', $this->data);
    }

    public function doMake(Request $request)
    {
        $max_buffer_qty = $this->countMaxBufferQuantity();
        $max_warehouse_qty = $this->countMaxWarehouseQuantity();

        if($request->warehouse_qty && !$request->buffer_qty) {
            $request->buffer_qty = 0;
            $request['buffer_qty'] = 0;
        }

        if($request->buffer_qty && !$request->warehouse_qty) {
            $request->warehouse_qty = 0;
            $request['warehouse_qty'] = 0;
        }

        $this->validate($request, [
//            'outsourcing_water' => 'required|integer|exists:outsourcing_waters,id',
            'purchase_invoice_no' => 'required|string',
            'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
            'buffer_qty' => 'required|integer|min:0|max:'.$max_buffer_qty,
            'warehouse_qty' => 'required|integer|min:0|max:'.$max_warehouse_qty,
            'delivery_at' => 'required|date'
        ]);

        if($request->buffer_qty < 1 && $request->warehouse_qty < 1){
            return back()
                ->withErrors(['message' => 'Jumlah salah satu galon minimal harus diisi']);
        }
        
        if((new OrderWater())->doMake($request, auth()->id())){
            return redirect(route('order.water.index'))
            ->with('success', 'Data telah berhasil dibuat');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doUpdate(Request $request)
    {        
        $orderWater = OrderWater::with('outsourcingDriver','order')->find($request->id);

        //check if driver_name is filled  or not
        if($orderWater->order->accepted_at == null){
            if($request->driver_name!=null){
                return back()
                ->withErrors(['message' => 'Terjadi kesalahan, nama pengemudi tidak boleh diubah!']);
            }
            $this->validate($request, [           
//                'outsourcing_water' => 'required|integer|exists:outsourcing_waters,id',
                'purchase_invoice_no' => 'required|string',
                'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
                'buffer_qty' => 'required|integer|min:0|max:'.$request->max_buffer_qty,
                'warehouse_qty' => 'required|integer|min:0|max:'.$request->max_warehouse_qty,
                'delivery_at' => 'required|date',
                'description' => 'required|string|regex:/^[^;]+$/'
                
            ]);          
        }else{
            $this->validate($request, [           
//                'outsourcing_water' => 'required|integer|exists:outsourcing_waters,id',
                'purchase_invoice_no' => 'required|string',
                'outsourcing_driver' => 'required|integer|exists:outsourcing_drivers,id',
                'driver_name' => 'required|string',
                'buffer_qty' => 'required|integer|min:0|max:'.$request->max_buffer_qty,
                'warehouse_qty' => 'required|integer|min:0|max:'.$request->max_warehouse_qty,
                'delivery_at' => 'required|date',
                'invoice_no_edit' => 'required|string',
                'price_edit' => 'required|integer|min:0',
                //'total_edit' => 'required|integer|min:0',
                'description' => 'required|string|regex:/^[^;]+$/'
                
            ]);
        }

        if($request->buffer_qty < 1 && $request->warehouse_qty < 1){
            return back()
                ->withErrors(['message' => 'Jumlah salah satu galon minimal harus diisi']);
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
            'driver_name' => 'required|string',
            'invoice_no' => 'required|string',
            'price' => 'required|integer|min:0'
        ]);

        $orderWater = OrderWater::with('order')->find($request->id);

        if( $orderWater->doConfirm($request) ){
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
            return 'Data telah berhasil diupdate';
        }else{
            return 'There is something wrong, please contact admin';
        }
    }

    public function doConfirmWithIssue(Request $request){

        $orderWater = OrderWater::find($request->id);

        // check if no type is selected
        if(!$request->typeGallonDriver && !$request->typeGallon && !$request->typeSeal && !$request->typeTissue){
            return back()
            ->withErrors(['message' => 'Tipe masalah belum dipilih!']);
        }
      
        $this->validate($request, [
            'driver_name' => 'required|string'
        ]); 

        $issueGallonDriver = new Issue();
        $issueGallon = new Issue();
        $issueSeal = new Issue();
        $issueTissue = new Issue();
        
        //validate request and make issue object according to the types checked
        if($request->typeGallonDriver){            
            $this->validate($request, [    
                'typeDriver' => 'required',                           
                'quantity_gallon_driver' => 'required|integer|min:1|max:'.$request->max_quantity,
                'description_gallon_driver' => 'required|string'
            ]); 
            $data = array(   
                'type' => $request->typeDriver,   
                'quantity' => $request->quantity_gallon_driver,
                'description' => $request->description_gallon_driver
            );
            $issueGallonDriver->doMakeIssueOrderWater($request,$data);            
        }
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
        if($request->typeGallonDriver && $request->typeGallon){
            $this->validate($request, [     
                'quantity_gallon_driver' => 'required|integer|min:1|max:'.($request->max_quantity-$request->quantity_gallon),                   
                'quantity_gallon' => 'required|integer|min:1|max:'.($request->max_quantity-$request->quantity_gallon_driver),
                
            ]); 
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

        if( $orderWater->doConfirmWithIssue($request,$issueGallonDriver,$issueGallon,$issueSeal,$issueTissue) ){
            return redirect(route('order.water.index'))
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
        
    }

    public function getAll()
    {
        $orderWaters = OrderWater::has('order')->with('outsourcingDriver','order','order.user','order.issues')->get();
    
        return json_encode($orderWaters);
    }
}
