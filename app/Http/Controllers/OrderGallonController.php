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
            return redirect(route('order.gallon.index'))
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
      
        if($orderGallon->doUpdate($request)){           
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

        if($orderGallon->doDelete($request->description, auth()->user()->id)){
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
        
        if($orderGallon->doConfirm($request->driver_name) ){
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
        
        if( $orderGallon->doCancel() ){
            return back()
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function getOrderGallons()
    {
        $orderGallons = OrderGallon::has('order')->with('outsourcingDriver','order','order.user')->get();
       
        return json_encode($orderGallons);
    }
}
