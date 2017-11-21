<?php

namespace App\Http\Controllers;

use App\Models\OrderCustomer;
use Illuminate\Http\Request;
use App\Models\Shipment;

class ShipmentController extends Controller
{
    protected $data = array();
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'shipment';
        $this->data['slug'] = '';
    }

    /*======= Page Methods =======*/
    public function index()
    {
        $this->data['breadcrumb'] = "Shipment";

        return view('shipment.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Shipment - Create";

        return view('shipment.make', $this->data);
    }

    public function track($shipment_id)
    {
        $shipment = $this->getShipmentById($shipment_id);
        if(!$shipment){
            return back()->withErrors(['Tidak ada data shipment']);
        }

        $this->data['shipment'] = $shipment;
        $this->data['breadcrumb'] = "Shipment - ".$shipment->user->full_name." (ID ".$shipment->id.") - Track";

        return view('shipment.track', $this->data);
    }

    /*======= Get Methods =======*/
    public function getAllFinished(){
        return Shipment::with([
            'user',
            'orderCustomers'=> function($query){
                $query->with('order');
            }])
            ->where('status', '=', 'Selesai')
            ->get();
    }

    public function getAllUnfinished(){
        return Shipment::with([
            'user',
            'orderCustomers'=> function($query){
                $query->with('order');
            }])
            ->whereIn('status', ['Draft', 'Proses'])
            ->get();
    }
    public function getAvailableShipmentsByDate(Request $request){
        return Shipment::with([
            'user',
            'orderCustomers'=> function($query){
                $query->with('order');
            }])
            ->where([
                ['delivery_at', $request->delivery_at],
                ['status', 'draft']
            ])
            ->whereIn('status', ['Draft', 'Proses'])
            ->get();
    }
    public function getShipmentById($shipment_id){
        return Shipment::with(['user', 'orderCustomers' => function($query){
            $query->with([
                'order' => function($query){
                    $query->with('user');
                },
                'customer']);
            }])
            ->find($shipment_id);
    }
    /*======= Do Methods =======*/
    public function doMake(Request $request){
        if($request->submit_type == "new"){
            $this->validate($request, [
                'order_ids' => 'exists:order_customers,id',
                'driver_id' => 'required|exists:users,id',
                'delivery_at' => 'required|date|after_or_equal:today'
            ]);

            $shipment = (new Shipment)->doMake($request);

            if(!$shipment){
                return back()->withErrors(['Mohon maaf, telah terjadi kesalahan']);
            }
        }
        else if($request->submit_type == "add"){
            $this->validate($request, [
                'order_ids' => 'required|exists:order_customers,id',
                'shipment_id' => 'required|exists:shipments,id',
                'delivery_at' => 'required|date|after_or_equal:today'
            ]);

            $shipment = Shipment::where([
                    ['delivery_at', $request->delivery_at],
                    ['status', 'draft']
                ])->find($request->shipment_id);

            if($shipment){
                if(!$shipment->doAddOrderToShipment($request)){
                    return back()->withErrors(['Mohon maaf, telah terjadi kesalahan']);
                }
            }
            else{
                return back()->withErrors(['Mohon maaf, telah terjadi kesalahan']);
            }
        }
        else{
            return back()->withErrors(['Mohon maaf, tipe submit salah']);
        }

        return back()->with('success', 'Berhasil membuat pengiriman');
    }

    public function doUpdate(Request $request){
        $this->validate($request, [
            'driver_id' => 'required|integer|exists:users,id',
            'delivery_at' => 'required|date',
            'status' => 'required|string|in:Draft,Proses,Selesai',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $shipment = Shipment::find($request->shipment_id);

        if(!$shipment->doUpdate($request)){
            return back()->withErrors(['Mohon maaf, telah terjadi kesalahan']);
        }

        return back()->with('success', 'Data telah berhasil diupdate');
    }

    public function doDelete(Request $request){
        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);

        $shipment = Shipment::find($request->shipment_id);

        if(!$shipment->doDelete($request)){
            return back()->withErrors(['Mohon maaf, telah terjadi kesalahan']);
        }

        return back()->with('success', 'Data telah berhasil diupdate');
    }
}
