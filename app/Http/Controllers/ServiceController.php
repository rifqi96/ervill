<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrderCustomer;
use App\Models\Shipment;
use Carbon\Carbon;

class ServiceController extends Controller
{
	public function api(Request $request){
		if($request->keyword){
			switch($request->keyword){
				case 'login':
					return $this->login($request);
					break;				
				
				//////////////this part below needs to be login first//////////////
				default:
					if( !$this->isLogin($request->user_id,$request->token) ){
						return $this->apiResponse(2,'Maaf, anda harus login ulang','Invalid Ervill token');
					}

					switch($request->keyword){
						case 'test':
							return $this->test($request);
							break;

						case 'today-shipments':
							return $this->getTodayShipments($request);
							break;

						case 'orders-by-shipment':
							return $this->getOrdersByShipment($request);
							break;

						case 'orders-history-by-shipment':
							return $this->getOrdersHistoryByShipment($request);
							break;

						case 'order-details':
							return $this->getOrderDetail($request);
							break;

						case 'signout':
							return $this->logout($request);
							break;

						default:
							return $this->apiResponse(0,'keyword salah','keyword salah');
							break;
					}
					
					break;
			}
		}else{
			return $this->apiResponse(0,'mohon isi keyword','mohon isi keyword');
		}
		
	}

    public function login($request){
    	$user = User::with('role')
        ->where('username', $request->username)->first();

        //check if username and password is correct
    	if($user && Hash::check($request->password, $user->password)){
            $token = str_random(60);
            $data = array(
                'token' => $token,
                'user' => $user->toArray()
            );

            //set token to random string
            $user->doUpdateApiToken($token);

            return $this->apiResponse(1,'berhasil login','',$data);
        }
        return $this->apiResponse(0,'akun tidak ditemukan','akun tidak ditemukan');
    }

    public function logout($request){
    	$user = User::where('id', $request->user_id)->first();

    	if($user){
    		//set token to null
            $user->doUpdateApiToken(null);

            $data = array(        
           		'success' => true
            );

            return $this->apiResponse(1,'berhasil logout','',$data);
        }
        return $this->apiResponse(0,'gagal logout','gagal logout');
    }

    public function getTodayShipments($request){
    	$today = Carbon::today();
    	$shipments = Shipment::where([
    		['user_id', $request->user_id],
    		['delivery_at',$today],
    		['status','!=','Selesai']])->get();
    
    	if( count($shipments) > 0 ){
	    	$data = array();

	    	$order_quantity = 0;
	    	$gallon_quantity = 0;

	    	//sort proses shipment then draft shipment
	    	$shipments_sorted = array();
	    	foreach ($shipments as $shipment) {
	    		if($shipment->status=="Draft"){
	    			array_push($shipments_sorted,$shipment);
	    		}else if($shipment->status=="Proses"){
	    			array_unshift($shipments_sorted,$shipment);
	    		}
	    	}
	   

	    	foreach($shipments_sorted as $shipment){
	    		//calculate amount of orders in a shipment
	    		$order_quantity = count($shipment->orderCustomers);
	 			$gallon_quantity = 0;
	    		foreach ($shipment->orderCustomers as $orderCustomer) {
	    			//calculate amount of gallons in an order
	    			$gallon_quantity += $orderCustomer->order->quantity;

	    		}
	    		array_push($data,[
	    			'id' => $shipment->id,
	    			'delivery_at' => $shipment->delivery_at,
	    			'status' => $shipment->status,
	    			'order_qty' => $order_quantity,
	    			'gallon_qty' => $gallon_quantity
	    		]);
	    	}
    	
    	
    		return $this->apiResponse(1,'berhasil memuat data shipment','berhasil memuat data shipment', $data);
    	}
    	return $this->apiResponse(1,'tidak ada pengiriman hari ini');
    }

    public function getOrdersByShipment($request){

    	if( !$request->shipment_id ){
    		return $this->apiResponse(0,'gagal memuat data order','gagal memuat data order, shipment id tidak ditemukan');
    	}    	

    	$orderCustomers = OrderCustomer::whereHas('shipment', function ($query) use($request) {
    		$query->where('user_id', $request->user_id);
    	})->where([
    		['shipment_id', $request->shipment_id],
    		['status','Proses']])
    	->get();
    
    	if( count($orderCustomers) > 0 ){
	    	$data = array();

	    	foreach($orderCustomers as $orderCustomer){	    		
	    		array_push($data,[
	    			'id' => $orderCustomer->id,
	    			'customer_name' => $orderCustomer->customer->name,
	    			'customer_address' => $orderCustomer->customer->address,
	    			'customer_phone' => $orderCustomer->customer->phone	    	
	    		]);
	    	}
    	
    	
    		return $this->apiResponse(1,'berhasil memuat data order','berhasil memuat data order', $data);
    	}
    	return $this->apiResponse(1,'tidak ada order pada pengiriman ini');
    }

    public function getOrdersHistoryByShipment($request){

    	if( !$request->shipment_id ){
    		return $this->apiResponse(0,'gagal memuat data order','gagal memuat data order, shipment id tidak ditemukan');
    	}    	

    	$orderCustomers = OrderCustomer::whereHas('shipment', function ($query) use($request) {
    		$query->where('user_id', $request->user_id);
		})->where([
    		['shipment_id', $request->shipment_id],
    		['status','!=','Proses'],
    		['status','!=','Draft']])
		->get();
    
    	if( count($orderCustomers) > 0 ){
	    	$data = array();

	    	foreach($orderCustomers as $orderCustomer){	    		
	    		array_push($data,[
	    			'id' => $orderCustomer->id,
	    			'customer_name' => $orderCustomer->customer->name,
	    			'customer_address' => $orderCustomer->customer->address,
	    			'customer_phone' => $orderCustomer->customer->phone,
	    			'status' => $orderCustomer->status	    	
	    		]);
	    	}
    	
    	
    		return $this->apiResponse(1,'berhasil memuat data order yang telah selesai','berhasil memuat data order yang telah selesai', $data);
    	}
    	return $this->apiResponse(1,'tidak ada order yang selesai pada pengiriman ini');
    }

    public function getOrderDetail($request){

    	if( !$request->order_id ){
    		return $this->apiResponse(0,'gagal memuat data detail order','gagal memuat data detail order, order customer id tidak ditemukan');
    	}    	

    	$orderCustomer = OrderCustomer::whereHas('shipment', function ($query) use($request) {
    		$query->where('user_id', $request->user_id);
    	})->where('id', $request->order_id)->first();
    
    	if( $orderCustomer ){
	    	$order_issues = array();

	    	//set order detail
	    	$order_detail = array([
	    		'id' => $orderCustomer->id,
	    		'customer_name' => $orderCustomer->customer->name,
	    		'customer_address' => $orderCustomer->customer->address,
	    		'customer_phone' => $orderCustomer->customer->phone,
	    		'gallon_qty' => $orderCustomer->order->quantity,
	    		'empty_gallon_qty' => $orderCustomer->empty_gallon_qty
	    	]);

	    	//set order issues to an array
	    	foreach($orderCustomer->order->issues as $issue){
	    		array_push($order_issues,[
	    			'id' => $issue->id,
	    			'description' => $issue->description,
	    			'type' => $issue->type,
	    			'quantity' => $issue->quantity
	    		]);
	    	}

	    	$data = array([
	    		'order' => $order_detail,
	    		'issues' => $order_issues
	    	]);
    	
    	
    		return $this->apiResponse(1,'berhasil memuat rincian order','berhasil memuat rincian order', $data);
    	}
    	return $this->apiResponse(0,'gagal memuat rincian order','gagal memuat rincian order');
    }

    ///////////change OC status, for testing only////////////
    public function test($request){
    	$orderCustomer = OrderCustomer::first();

    	if( $orderCustomer ){
    		$orderCustomer->doUpdateStatus($request->status);         

            return $this->apiResponse(1,'berhasil mengubah data','');
        }
        return $this->apiResponse(0,'gagal mengubah data','gagal mengubah data');
    }

    public function apiResponse($status,$message='',$error='',$data=array()){
    	return json_encode(array(
    		'status' => $status,
    		'message' => $message,
    		'error' => $error,
    		'data' => $data
    	));
    }

    //check id and token is valid or not
    public function isLogin($id,$token){
    	if(!$id || !$token){
    		return false;
    	}
    	$user = User::where([['id',$id],['ervill_token',$token]])->first();
    	return $user?true:false;
    }


}
