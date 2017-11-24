<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;

class ServiceController extends Controller
{
	public function api(Request $request){
		if($request->keyword){
			switch($request->keyword){
				case 'login':
					return $this->login($request);
					break;

				default:
					return $this->apiResponse(0,'keyword salah','keyword salah');
					break;
			}
		}else{
			return $this->apiResponse(0,'mohon isi keyword','mohon isi keyword');
		}
		
	}

    public function login($request){
    	$user = User::with('role')
        ->where('username', $request->username)->first();

    	if($user && Hash::check($request->password, $user->password)){
            $token = str_random(60);
            $data = array(
                'token' => $token,
                'user' => $user->toArray()
            );

            $user->doUpdateApiToken($token);

            return $this->apiResponse(1,'berhasil login','',$data);
        }
        return $this->apiResponse(0,'gagal login','');
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
