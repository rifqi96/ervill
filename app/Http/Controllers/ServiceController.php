<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrderCustomer;
use App\Models\Shipment;
use Carbon\Carbon;
use App\Models\Issue;
use App\Models\UserThirdParty;
use App\Models\OrderCustomerReturn;
use App\Models\OcHeaderInvoice;
use App\Models\ReHeaderInvoice;
use App\Http\Resources\UserResource;
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
						// case 'test':
						// 	return $this->test($request);
						// 	break;

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

						case 'start-shipment':
							return $this->startShipment($request);
							break;

						case 'finish-shipment':
							return $this->finishShipment($request);
							break;

						case 'drop-gallon':
							return $this->dropGallon($request);
							break;

						case 'add-issue':
							return $this->addIssue($request);
							break;

						case 'remove-issue':
							return $this->removeIssue($request);
							break;

						case 'cancel-transaction':
							return $this->cancelTransaction($request);
							break;

						case 'shipments-history':
							return $this->getFinishedShipments($request);
							break;

						case 'edit-order':
							return $this->editOrder($request);
							break;

						case 'add-fcm-token':
							return $this->addFcmToken($request);
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
			$third_party = UserThirdParty::where('user_id', $user->id)->first();
            $token = str_random(60);
            $data = array(
                'token' => $token,
				'user' => $user->toArray(),
				'fcm_token' => $third_party ? $third_party->fcm_token : null
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

	    	//$order_quantity = 0;
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
                $invoice_id_arr = array();           
                $gallon_quantity = 0;

	    		//calculate amount of orders in a shipment	    	
                //if($shipment->orderCustomers){
                    foreach ($shipment->ocHeaderInvoices as $ocHeaderInvoice) {
                        foreach ($ocHeaderInvoice->orderCustomers as $orderCustomer) {
                            
                            //galon isi
                            if($orderCustomer->price_id=="1" || $orderCustomer->price_id=="2" || $orderCustomer->price_id=="3" || $orderCustomer->price_id=="4" || $orderCustomer->price_id=="8" || $orderCustomer->price_id=="9" || $orderCustomer->price_id=="10" || $orderCustomer->price_id=="11"){
                                 $gallon_quantity += $orderCustomer->quantity;
                             }


                            //calculate amount of gallons in an order
                            //$gallon_quantity += $orderCustomer->quantity;
                            //$gallon_quantity += $orderCustomer->orderCustomer->additional_quantity;
                        }                                 
                        
                    }

                    array_push($data,[
                        'id' => $shipment->id,
                        'delivery_at' => $shipment->delivery_at,
                        'status' => $shipment->status,
                        'order_qty' => count($shipment->ocHeaderInvoices) + count($shipment->reHeaderInvoices),
                        'gallon_qty' => $gallon_quantity
                    ]);
                //}
			
	    	}
    	
    	
    		return $this->apiResponse(1,'berhasil memuat data pengiriman','berhasil memuat data pengiriman', $data);
    	}
    	return $this->apiResponse(1,'tidak ada pengiriman hari ini');
    }

    public function getOrdersByShipment($request){

    	if( !$request->shipment_id ){
    		return $this->apiResponse(0,'gagal memuat data order','gagal memuat data order, shipment id tidak ditemukan');
    	}    	

    	$today = Carbon::today();

    	// $orderCustomers = OrderCustomer::whereHas('shipment', function ($query) use($request,$today) {
    	// 	$query->where([
    	// 		['user_id', $request->user_id],
    	// 		['delivery_at',$today]]);
    	// })->where([
    	// 	['shipment_id', $request->shipment_id],
    	// 	['status','Proses']])
    	// ->get();

        $ocHeaderInvoices = OcHeaderInvoice::whereHas('shipment', function ($query) use($request,$today){
            $query->where([
                ['user_id', $request->user_id],
                ['delivery_at',$today]]
            );
        })->where([
            ['shipment_id', $request->shipment_id],
            ['status','Proses']])
        ->get();

        $reHeaderInvoices = ReHeaderInvoice::whereHas('shipment', function ($query) use($request,$today){
            $query->where([
                ['user_id', $request->user_id],
                ['delivery_at',$today]]
            );
        })->where([
            ['shipment_id', $request->shipment_id],
            ['status','Proses']])
        ->get();

        
    	if( count($ocHeaderInvoices) > 0 || count($reHeaderInvoices) > 0 ){
	    	$data = array();

            //ocheaderinvoices
	    	foreach($ocHeaderInvoices as $ocHeaderInvoice){	  
                if($ocHeaderInvoice->status=="Proses"){
                    if(count($ocHeaderInvoice->orderCustomers)>0){
                        array_push($data,[
                            'id' => $ocHeaderInvoice->id,
                            'type' => 'order',
                            'customer_name' => $ocHeaderInvoice->customer->name,
                            'customer_address' => $ocHeaderInvoice->customer->address,
                            'customer_phone' => $ocHeaderInvoice->customer->phone
                        ]);
                    }
                }           		
	    		
	    	}

            //reheaderinvoice
            foreach($reHeaderInvoices as $reHeaderInvoice){     
                if($reHeaderInvoice->status=="Proses"){
                    if(count($reHeaderInvoice->orderCustomerReturnInvoices)>0){
                        array_push($data,[
                            'id' => $reHeaderInvoice->id,
                            'type' => 'return',
                            'customer_name' => $reHeaderInvoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->name,
                            'customer_address' => $reHeaderInvoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->address,
                            'customer_phone' => $reHeaderInvoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->phone         
                        ]);
                    }
                }                   
                
            }
    	
    	
    		return $this->apiResponse(1,'berhasil memuat data order','berhasil memuat data order', $data);
    	}
    	return $this->apiResponse(1,'tidak ada order pada pengiriman ini');
    }

    public function getOrdersHistoryByShipment($request){

  //   	if( !$request->shipment_id ){
  //   		return $this->apiResponse(0,'gagal memuat data order','gagal memuat data order, shipment id tidak ditemukan');
  //   	}    	


  //   	$orderCustomers = OrderCustomer::whereHas('shipment', function ($query) use($request) {
  //   		$query->where('user_id', $request->user_id);
		// })->where([
  //   		['shipment_id', $request->shipment_id],
  //   		['status','!=','Proses'],
  //   		['status','!=','Draft']])
		// ->get();
    
  //   	if( count($orderCustomers) > 0 ){
	 //    	$data = array();

	 //    	foreach($orderCustomers as $orderCustomer){	    		
	 //    		array_push($data,[
	 //    			'id' => $orderCustomer->orderCustomers[0]->oc_header_invoice_id,
	 //    			'customer_name' => $orderCustomer->customer->name,
	 //    			'customer_address' => $orderCustomer->customer->address,
	 //    			'customer_phone' => $orderCustomer->customer->phone,
	 //    			'status' => $orderCustomer->status	    	
	 //    		]);
	 //    	}
    	
    	
  //   		return $this->apiResponse(1,'berhasil memuat data order yang telah selesai','berhasil memuat data order yang telah selesai', $data);
  //   	}
  //   	return $this->apiResponse(1,'tidak ada order yang selesai pada pengiriman ini');


        if( !$request->shipment_id ){
            return $this->apiResponse(0,'gagal memuat data order','gagal memuat data order, shipment id tidak ditemukan');
        }       

        $ocHeaderInvoices = OcHeaderInvoice::whereHas('shipment', function ($query) use($request){
            $query->where('user_id', $request->user_id);
        })->where([
            ['shipment_id', $request->shipment_id],
            ['status','!=','Proses'],
            ['status','!=','Draft']])
        ->get();

        $reHeaderInvoices = ReHeaderInvoice::whereHas('shipment', function ($query) use($request){
            $query->where('user_id', $request->user_id);
        })->where([
            ['shipment_id', $request->shipment_id],
            ['status','!=','Proses'],
            ['status','!=','Draft']])
        ->get();

        
        if( count($ocHeaderInvoices) > 0 || count($reHeaderInvoices) > 0 ){
            $data = array();

            //ocheaderinvoices
            foreach($ocHeaderInvoices as $ocHeaderInvoice){   
                //if($ocHeaderInvoice->status=="Proses"){
                    if(count($ocHeaderInvoice->orderCustomers)>0){
                        array_push($data,[
                            'id' => $ocHeaderInvoice->id,
                            'type' => 'order',
                            'customer_name' => $ocHeaderInvoice->customer->name,
                            'customer_address' => $ocHeaderInvoice->customer->address,
                            'customer_phone' => $ocHeaderInvoice->customer->phone,
                            'status' => $ocHeaderInvoice->status         
                        ]);
                    }
                //}                   
                
            }

            //reheaderinvoice
            foreach($reHeaderInvoices as $reHeaderInvoice){     
                //if($reHeaderInvoice->status=="Proses"){
                    if(count($reHeaderInvoice->orderCustomerReturnInvoices)>0){
                        array_push($data,[
                            'id' => $reHeaderInvoice->id,
                            'type' => 'return',
                            'customer_name' => $reHeaderInvoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->name,
                            'customer_address' => $reHeaderInvoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->address,
                            'customer_phone' => $reHeaderInvoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->phone,
                            'status' => $reHeaderInvoice->status         
                        ]);
                    }
                //}                   
                
            }
        
        
            return $this->apiResponse(1,'berhasil memuat data order','berhasil memuat data order', $data);
        }
        return $this->apiResponse(1,'tidak ada order pada pengiriman ini');
    }

    public function getOrderDetail($request){

    	if( !$request->order_id ){
    		return $this->apiResponse(0,'gagal memuat data detail order','gagal memuat data detail order, order customer id tidak ditemukan');
    	}    	

        $gallon_qty=0;
        $ervill_empty_gallon_qty=0;
        $non_ervill_empty_gallon_qty=0;
        $total = 0;
        $customer_name = "";
        $customer_address = "";
        $customer_phone = "";

    	// $orderCustomer = OrderCustomer::whereHas('shipment', function ($query) use($request) {
    	// 	$query->where('user_id', $request->user_id);
    	// })->where('id', $request->order_id)->first();
        $header_invoice = OcHeaderInvoice::whereHas('shipment', function ($query) use($request){
            $query->where('user_id', $request->user_id);
        })->where('id', $request->order_id)->first();

        $re_header_invoice = ReHeaderInvoice::whereHas('shipment', function ($query) use($request){
            $query->where('user_id', $request->user_id);
        })->where('id', $request->order_id)->first();

        if( $header_invoice ){
            // $gallon_qty += ($header_invoice->orderCustomers[0]->orderCustomer->order->quantity + $header_invoice->orderCustomers[0]->orderCustomer->additional_quantity);

             //  $ervill_empty_gallon_qty += $header_invoice->orderCustomers[0]->orderCustomer->empty_gallon_quantity;
             
             // if($header_invoice->orderCustomers[0]->orderCustomer->purchase_type == "non_ervill"){
             //    if($header_invoice->orderCustomers[0]->orderCustomer->is_new=="true"){
             //        $non_ervill_empty_gallon_qty += $header_invoice->orderCustomers[0]->orderCustomer->order->quantity;
             //    }else if($header_invoice->orderCustomers[0]->orderCustomer->is_new=="false"){
             //        $non_ervill_empty_gallon_qty += $header_invoice->orderCustomers[0]->orderCustomer->additional_quantity;
             //    }
             // }
            foreach ($header_invoice->orderCustomers as $orderCustomer) {

                //galon isi
                if($orderCustomer->price_id=="1" || $orderCustomer->price_id=="2" || $orderCustomer->price_id=="3" || $orderCustomer->price_id=="4" || $orderCustomer->price_id=="8" || $orderCustomer->price_id=="9" || $orderCustomer->price_id=="10" || $orderCustomer->price_id=="11"){
                     $gallon_qty += $orderCustomer->quantity;
                 }

                 //isi ulang
                 if($orderCustomer->price_id=="1" || $orderCustomer->price_id=="8"){
                    $ervill_empty_gallon_qty += $orderCustomer->quantity;
                 }
                 //tukar galon non_ervill
                 if($orderCustomer->price_id=="3" || $orderCustomer->price_id=="10"){
                    $non_ervill_empty_gallon_qty += $orderCustomer->quantity;
                 }

                 $total += $orderCustomer->subtotal;
                 $customer_name = $header_invoice->customer->name;
                 $customer_address = $header_invoice->customer->address;
                 $customer_phone = $header_invoice->customer->phone;
                 
            }

            if($header_invoice->is_free=="true"){
                $total = 0;
            }
    
	    	$order_issues = array();

	    	//set order detail
	    	$order_detail = (object) array(
	    		'id' => $header_invoice->id,
                'type' => 'order',
	    		'customer_name' => $customer_name,
	    		'customer_address' => $customer_address,
	    		'customer_phone' => $customer_phone,
	    		'gallon_qty' => $gallon_qty,
	    		'ervill_empty_gallon_qty' => $ervill_empty_gallon_qty,
                'non_ervill_empty_gallon_qty' => $non_ervill_empty_gallon_qty,
                'total' => $total,
	    		'status' => $header_invoice->status
	    	);



	    	//set order issues to an array
	    	// foreach($orderCustomer->order->issues as $issue){
	    	// 	array_push($order_issues,[
	    	// 		'id' => $issue->id,
	    	// 		'description' => $issue->description,
	    	// 		'type' => $issue->type,
	    	// 		'quantity' => $issue->quantity
	    	// 	]);
	    	// }

	    	$data = array(
	    		'order' => $order_detail,
	    		'issues' => $order_issues
	    	);
    	
    	
    		return $this->apiResponse(1,'berhasil memuat rincian order','berhasil memuat rincian order', $data);
    	}else if( $re_header_invoice ){    
            $total = 0;
            $order_issues = array();
            foreach ($re_header_invoice->orderCustomerReturnInvoices as $orderCustomerReturnInvoice) {
                $total -= $orderCustomerReturnInvoice->subtotal;
            }     

            if($re_header_invoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->is_non_refund == "true"){
                $total = 0;
            }


            //set order detail
            $order_detail = (object) array(
                'id' => $re_header_invoice->id,
                'type' => 'return',
                'customer_name' => $re_header_invoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->name,
                'customer_address' => $re_header_invoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->address,
                'customer_phone' => $re_header_invoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->customer->phone,
                'is_non_refund' => $re_header_invoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->is_non_refund,
                'filled_gallon_qty' => $re_header_invoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->filled_gallon_quantity,
                'empty_gallon_qty' => $re_header_invoice->orderCustomerReturnInvoices[0]->orderCustomerReturn->empty_gallon_quantity,             
                'total' => $total,
                'status' => $re_header_invoice->status
            );


            $data = array(
                'order' => $order_detail,
                'issues' => $order_issues
            );

            return $this->apiResponse(1,'berhasil memuat rincian order','berhasil memuat rincian order', $data);
        }
    	return $this->apiResponse(0,'gagal memuat rincian order','gagal memuat rincian order');
    }

    public function startShipment($request){

    	if( !$request->shipment_id ){
    		return $this->apiResponse(0,'gagal memulai pengiriman','gagal memulai pengiriman, shipment id tidak ditemukan');
    	}    	

    	$today = Carbon::today();

		$shipment = Shipment::where([
    		['id', $request->shipment_id],
    		['user_id', $request->user_id],
    		['delivery_at',$today],
    		['status','Draft']])->first();
    
    	if( $shipment ){
    		if($shipment->doStartShipment($request->user_id)){

                foreach ($shipment->ocHeaderInvoices as $ocHeaderInvoice) {
                    if(!$ocHeaderInvoice->doStartShipment()){
                        return $this->apiResponse(0,'terjadi kesalahan, tidak dapat merubah status oc_header','terjadi kesalahan, tidak dapat merubah status oc_header');
                    }                                        
                }

                foreach ($shipment->reHeaderInvoices as $reHeaderInvoice) {
                    if(!$reHeaderInvoice->doStartShipment()){
                        return $this->apiResponse(0,'terjadi kesalahan, tidak dapat merubah status re_header','terjadi kesalahan, tidak dapat merubah status re_header');
                    }                                        
                }

    			// foreach($shipment->orderCustomers as $orderCustomer){
    			// 	if(!$orderCustomer->doStartShipment()){
    			// 		return $this->apiResponse(0,'terjadi kesalahan, tidak dapat merubah status order customer','terjadi kesalahan, tidak dapat merubah status order customer');
    			// 	}
    			// }

    			$data = array(
		    		'success' => 'true'
		    	);

    			return $this->apiResponse(1,'berhasil memulai pengiriman','berhasil memulai pengiriman', $data);    			
    		}	    	
    	
    		return $this->apiResponse(0,'gagal memulai pengiriman, sedang ada pengiriman yang berlangsung','gagal memulai pengiriman, sedang ada pengiriman yang berlangsung');
    	}
    	return $this->apiResponse(0,'gagal memulai pengiriman, shipment ini tidak bisa dimulai pengirimannya','gagal memulai pengiriman, shipment ini tidak bisa dimulai pengirimannya');
    	
    }

    public function finishShipment($request){

    	if( !$request->shipment_id ){
    		return $this->apiResponse(0,'gagal mengakhiri pengiriman','gagal mengakhiri pengiriman, shipment id tidak ditemukan');
    	}    

    	$today = Carbon::today();

		$shipment = Shipment::where([
    		['id', $request->shipment_id],
    		['user_id', $request->user_id],
    		['delivery_at',$today],
    		['status','Proses']])->first();
    
    	if( $shipment ){    			   
            // foreach ($shipment->ocHeaderInvoices as $ocHeaderInvoice) {
            //     if(!$ocHeaderInvoice->doFinishShipment()){
            //         return $this->apiResponse(0,'gagal mengakhiri pengiriman, tidak dapat merubah status oc_header','gagal mengakhiri pengiriman, tidak dapat merubah status oc_header');
            //     }                                        
            // } 
            // foreach ($shipment->reHeaderInvoices as $reHeaderInvoice) {
            //     if(!$reHeaderInvoice->doFinishShipment()){
            //         return $this->apiResponse(0,'gagal mengakhiri pengiriman, tidak dapat merubah status re_header','gagal mengakhiri pengiriman, tidak dapat merubah status re_header');
            //     }                                        
            // } 

			$data = array(
	    		'success' => 'true'
	    	);    			   			
    		

            if(!$shipment->doFinishShipment()){
                return $this->apiResponse(0,'gagal mengakhiri pengiriman, masih ada order yang belum dikirim','gagal mengakhiri pengiriman, masih ada order yang belum dikirim');
            }	    	
    	   
            return $this->apiResponse(1,'berhasil mengakhiri pengiriman','berhasil mengakhiri pengiriman', $data); 
    		
    	}
    	return $this->apiResponse(0,'gagal mengakhiri pengiriman, pengiriman ini tidak bisa diakhiri pengirimannya','gagal mengakhiri pengiriman, pengiriman ini tidak bisa diakhiri pengirimannya');	
    	
    }

    public function dropGallon($request){

    	if( !$request->order_id ){
    		return $this->apiResponse(0,'gagal memproses order','gagal memproses order, order customer id tidak ditemukan');
    	}    

    	$today = Carbon::today();

    	// $orderCustomer = OrderCustomer::whereHas('shipment', function ($query) use($request,$today) {
    	// 	$query->where([
    	// 		['user_id', $request->user_id],
    	// 		['delivery_at',$today],
    	// 		['status','Proses']]);
    	// })->where([
    	// 	['id', $request->order_id],
    	// 	['status','Proses']])
    	// ->first();

        $header_invoice = OcHeaderInvoice::whereHas('shipment', function ($query) use($request,$today){
            $query->where([
                ['user_id', $request->user_id],
                ['delivery_at',$today],
                ['status','Proses']]);
        })->where([
            ['id', $request->order_id],
            ['status','Proses']])
        ->first();

        $re_header_invoice = ReHeaderInvoice::whereHas('shipment', function ($query) use($request,$today){
            $query->where([
                ['user_id', $request->user_id],
                ['delivery_at',$today],
                ['status','Proses']]);
        })->where([
            ['id', $request->order_id],
            ['status','Proses']])
        ->first();
    
    	if( $header_invoice ){
    		if($header_invoice->doDropGallon()){	   
    			$data = array(
		    		'success' => 'true'
		    	);
    			return $this->apiResponse(1,'berhasil memproses order','berhasil memproses order', $data);    	
    		}	    	
    	
    		return $this->apiResponse(0,'gagal memproses order, status order tidak dapat dirubah','gagal memproses order, status order tidak dapat dirubah');
    	}else if( $re_header_invoice ){
            if($re_header_invoice->doDropGallon()){       
                $data = array(
                    'success' => 'true'
                );
                return $this->apiResponse(1,'berhasil memproses order','berhasil memproses order', $data);      
            }           
        
            return $this->apiResponse(0,'gagal memproses order, status order tidak dapat dirubah','gagal memproses order, status order tidak dapat dirubah');
        }
    	return $this->apiResponse(0,'gagal memproses order, order ini tidak bisa diproses','gagal memproses order, order ini tidak bisa diproses');	
    	
    }

    public function addIssue($request){
    	if( !$request->order_id ){
    		return $this->apiResponse(0,'gagal menambahkan masalah','gagal menambahkan masalah, order customer id tidak ditemukan');
    	}   

    	$today = Carbon::today();

    	$orderCustomer = OrderCustomer::whereHas('shipment', function ($query) use($request,$today) {
    		$query->where([
    			['user_id', $request->user_id],
    			['delivery_at',$today],
    			['status','Proses']]);
    	})->where('id', $request->order_id)
    	->first();    	


    	if( $orderCustomer ){
    		$issue = new Issue();
    		if($issue->doMakeIssueOrderCustomer($orderCustomer->order, $request)){	   

    			$data = array(
		    		'success' => 'true'
		    	);

    			return $this->apiResponse(1,'berhasil menambahkan masalah','berhasil menambahkan masalah', $data);    			
    		}	    	
    	
    		return $this->apiResponse(0,'gagal menambahkan masalah, terjadi kesalahan di sistem','gagal menambahkan masalah, terjadi kesalahan di sistem');
    	}
    	return $this->apiResponse(0,'gagal menambahkan masalah, order ini tidak bisa diproses','gagal menambahkan masalah, order ini tidak bisa diproses');	

    }

    public function removeIssue($request){
    	if( !$request->issue_id ){
    		return $this->apiResponse(0,'gagal menghapus masalah','gagal menghapus masalah, issue id tidak ditemukan');
    	}   

    	$today = Carbon::today();

    	$issue = Issue::whereHas('order.orderCustomer.shipment', function ($query) use($request,$today) {
    		$query->where([
    			['user_id', $request->user_id],
    			['delivery_at',$today],
    			['status','Proses']]);
    	})->where('id', $request->issue_id)
    	->first();  

    	//$issue = Issue::find($request->issue_id);	

    	if( $issue ){    		
    		if( $issue->doDelete() ){	    

    			$data = array(
		    		'success' => 'true'
		    	);

    			return $this->apiResponse(1,'berhasil menghapus masalah','berhasil menghapus masalah', $data);    			
    		}	    	
    	
    		return $this->apiResponse(0,'gagal menghapus masalah, terjadi kesalahan di sistem','gagal menghapus masalah, terjadi kesalahan di sistem');
    	}
    	return $this->apiResponse(0,'gagal menghapus masalah, masalah ini tidak bisa diproses','gagal menghapus masalah, masalah ini tidak bisa diproses');	

    }

    public function cancelTransaction($request){
    	if( !$request->order_id ){
    		return $this->apiResponse(0,'gagal membatalkan order','gagal membatalkan order, order customer id tidak ditemukan');
    	}    

    	$today = Carbon::today();


        $header_invoice = OcHeaderInvoice::whereHas('shipment', function ($query) use($request,$today){
            $query->where([
                ['user_id', $request->user_id],
                ['delivery_at',$today],
                ['status','Proses']]);
        })->where([
            ['id', $request->order_id],
            ['status','Proses']])
        ->first();

        $re_header_invoice = ReHeaderInvoice::whereHas('shipment', function ($query) use($request,$today){
            $query->where([
                ['user_id', $request->user_id],
                ['delivery_at',$today],
                ['status','Proses']]);
        })->where([
            ['id', $request->order_id],
            ['status','Proses']])
        ->first();

    	// $orderCustomer = OrderCustomer::whereHas('shipment', function ($query) use($request,$today) {
    	// 	$query->where([
    	// 		['user_id', $request->user_id],
    	// 		['delivery_at',$today],
    	// 		['status','Proses']]);
    	// })->where([
    	// 	['id', $request->order_id],
    	// 	['status','Proses']])
    	// ->first();
    
    	if( $header_invoice ){
    		//$issue = new Issue();
    		//if($issue->doCancelTransaction($orderCustomer->order)){	

            if($header_invoice->doCancelTransaction()){ 
    			$data = array(
		    		'success' => 'true'
		    	);

    			return $this->apiResponse(1,'berhasil membatalkan order','berhasil membatalkan order', $data);    			
    		}	    	
    	
    		return $this->apiResponse(0,'gagal membatalkan order, terjadi kesalahan di sistem','gagal membatalkan order, terjadi kesalahan di sistem');
    	}else if( $re_header_invoice ){
            //$issue = new Issue();
            //if($issue->doCancelTransaction($orderCustomer->order)){   

            if($re_header_invoice->doCancelTransaction()){ 
                $data = array(
                    'success' => 'true'
                );

                return $this->apiResponse(1,'berhasil membatalkan order return','berhasil membatalkan order return', $data);              
            }           
        
            return $this->apiResponse(0,'gagal membatalkan order return, terjadi kesalahan di sistem','gagal membatalkan order return, terjadi kesalahan di sistem');
        }
    	return $this->apiResponse(0,'gagal membatalkan order, order ini tidak bisa diproses','gagal membatalkan order, order ini tidak bisa diproses');	
    }

    public function getFinishedShipments($request){
    	
    	if($request->date){    		
    		$shipments = Shipment::where([
	    		['user_id', $request->user_id],
	    		['delivery_at',Carbon::parse($request->date)],
	    		['status','Selesai']])->get();
    	}else{
    		$today = Carbon::today();
    		$shipments = Shipment::where([
	    		['user_id', $request->user_id],
	    		['delivery_at',$today],
	    		['status','Selesai']])->get();
    	}
    	
    
    	if( count($shipments) > 0 ){
	    	$data = array();

	    	//$order_quantity = 0;
	    	//$gallon_quantity = 0;	   


            foreach($shipments as $shipment){
                $invoice_id_arr = array();           
                $gallon_quantity = 0;

                //calculate amount of orders in a shipment          
                //if($shipment->orderCustomers){
                    foreach ($shipment->ocHeaderInvoices as $ocHeaderInvoice) {
                        foreach ($ocHeaderInvoice->orderCustomers as $orderCustomer) {
                            
                            //galon isi
                            if($orderCustomer->price_id=="1" || $orderCustomer->price_id=="2" || $orderCustomer->price_id=="3" || $orderCustomer->price_id=="4" || $orderCustomer->price_id=="8" || $orderCustomer->price_id=="9" || $orderCustomer->price_id=="10" || $orderCustomer->price_id=="11"){
                                 $gallon_quantity += $orderCustomer->quantity;
                             }
                            
                            //calculate amount of gallons in an order
                            // $gallon_quantity += $ocHeaderInvoice->orderCustomers[0]->orderCustomer->order->quantity;
                            // $gallon_quantity += $ocHeaderInvoice->orderCustomers[0]->orderCustomer->additional_quantity;
                        }                                 
                        
                    }

                    array_push($data,[
                        'id' => $shipment->id,
                        'delivery_at' => $shipment->delivery_at,
                        'status' => $shipment->status,
                        'order_qty' => count($shipment->ocHeaderInvoices) + count($shipment->reHeaderInvoices),
                        'gallon_qty' => $gallon_quantity
                    ]);
                //}
            
            }
            /////////////////////////////////////// 	
	   

	    // 	foreach($shipments as $shipment){
	    // 		//calculate amount of orders in a shipment
	    // 		$order_quantity = count($shipment->orderCustomers);
	 			// $gallon_quantity = 0;
	    // 		foreach ($shipment->orderCustomers as $orderCustomer) {
	    // 			//calculate amount of gallons in an order
	    // 			$gallon_quantity += $orderCustomer->order->quantity;

	    // 		}
	    // 		array_push($data,[
	    // 			'id' => $shipment->id,
	    // 			'delivery_at' => $shipment->delivery_at,
	    // 			'status' => $shipment->status,
	    // 			'order_qty' => $order_quantity,
	    // 			'gallon_qty' => $gallon_quantity
	    // 		]);
	    // 	}
    	
    	
    		return $this->apiResponse(1,'berhasil memuat data pengiriman','berhasil memuat data pengiriman', $data);
    	}
    	return $this->apiResponse(1,'tidak ada pengiriman yang selesai hari ini');
    }
    
    public function editOrder($request){

    	if( !$request->order_id ){
    		return $this->apiResponse(0,'gagal merubah data order','gagal merubah data order, order customer id tidak ditemukan');
    	}    

    	if( $request->gallon_qty===null || $request->empty_gallon_qty===null || $request->description===null){
    		return $this->apiResponse(0,'gagal merubah data order, data belum lengkap','gagal merubah data order, data belum lengkap'); 
    	}

    	$today = Carbon::today();

    	$orderCustomer = OrderCustomer::whereHas('shipment', function ($query) use($request,$today) {
    		$query->where([
    			['user_id', $request->user_id],
    			['delivery_at',$today],
    			['status','Proses']]);
    	})->where([
    		['id', $request->order_id],
    		['status','Proses']])
    	->first();
    
    	if( $orderCustomer ){
    		if($orderCustomer->doEditOrder($request)){	    

    			$data = array(
		    		'success' => 'true'
		    	);

    			return $this->apiResponse(1,'berhasil merubah data order','berhasil merubah data order', $data);    			
    		}	    	
    	
    		return $this->apiResponse(0,'gagal merubah data order, terjadi kesalahan di sistem','gagal merubah data order, terjadi kesalahan di sistem');
    	}
    	return $this->apiResponse(0,'gagal merubah data order, order ini tidak bisa diproses','gagal merubah data order, order ini tidak bisa diproses');	
    	
	}
	
	public function addFcmToken($request) {
		$third_party = UserThirdParty::where('user_id', $request->user_id)->first();

		if(!$third_party){
			if((new UserThirdParty())->addFcmToken($request->user_id, $request->fcm_token)){
				return $this->apiResponse(1,'Berhasil','Berhasil menambahkan fcm token', array( 'success' => true ));
			}
			return $this->apiResponse(0,'Gagal','Gagal menambahkan fcm token');
		}
		else if($third_party->fcm_token != $request->fcm_token){
			if($third_party->updateFcmToken($request->fcm_token)) {
				return $this->apiResponse(1,'Berhasil','Berhasil mengupdate fcm token', array( 'success' => true ));
			}
			return $this->apiResponse(0,'Gagal','Gagal menambahkan fcm token');
		}
		else{
			return $this->apiResponse(0,'Gagal','Gagal menambahkan fcm token');
		}
	}

    ///////////change OC status, for testing only////////////
    // public function test($request){
    // 	$orderCustomer = OrderCustomer::first();

    // 	if( $orderCustomer ){
    // 		$orderCustomer->doUpdateStatus($request->status);         

    //         return $this->apiResponse(1,'berhasil mengubah data','');
    //     }
    //     return $this->apiResponse(0,'gagal mengubah data','gagal mengubah data');
    // }

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
