<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
