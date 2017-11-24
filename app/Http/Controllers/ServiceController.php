<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class ServiceController extends Controller
{
	public function api(Request $request){
		if($request->keyword){
			switch($request->keyword){
				case 'login':
					login($request);
					break;

				default: 
					return $this->apiResponse(0,'keyword salah','keyword salah');
					break;
			}
		}else{
			return $this->$this->apiResponse(0,'mohon isi keyword','mohon isi keyword');
		}
		
	}

    public function login($request){
    	$user = User::where([['username',$request->username],['password', bcrypt($request->password)]])->get();
		$token = str_random(60);
		$data = array(
			'token' => $token,
			'user' => $user
		);

		$user->doUpdateApiToken($token);

		return $this->apiResponse(1,'berhasil login','',$data);
    }

    public function apiResponse($status,$message='',$error='',$data=array()){
    	return json_encode(array(
    		'status' => $status,
    		'message' => $message,
    		'error' => $error,
    		'data' => $data
    	));
    }


}
