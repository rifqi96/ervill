<?php

namespace App\Http\Controllers;

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
        $this->data['breadcrumb'] = "Shipment - Pak Tarjo (ID ".$shipment_id.") - Track";

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

    /*======= Do Methods =======*/
}
