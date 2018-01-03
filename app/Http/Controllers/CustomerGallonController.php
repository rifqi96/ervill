<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerGallon;

class CustomerGallonController extends Controller
{
	public function __construct(){	      
        $this->middleware('SuperadminAndAdmin');
    }

    public function getCustomerGallon(Request $request){
    	$chosenCustomerGalllons = CustomerGallon::where('customer_id',$request->customer_id)->get();
    	$gallon_quantity = 0;
    	foreach ($chosenCustomerGalllons as $chosenCustomerGalllon) {
    		$gallon_quantity += $chosenCustomerGalllon->qty;
    	}
    	return $gallon_quantity;
    }
}
