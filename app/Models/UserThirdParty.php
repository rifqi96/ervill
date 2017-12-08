<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserThirdParty extends Model
{
    public function addFcmToken($user_id, $fcm_token) {
        $this->user_id = $user_id;
        $this->fcm_token = $fcm_token;
        return $this->save();
    }

    public function updateFcmToken($fcm_token){
        $this->fcm_token = $fcm_token;
        return $this->save();
    }

    public function send_fcm_notification($msg) {
        $APIKEY = "AIzaSyCXWFd--vx5D8x-IkgX6u8ji7ximzYc-wc";

        $headers = array(
            'Authorization: key='.$APIKEY,
            'Content-Type: application/json'
        );

        $i = 0;
        $msg['token'] = $this->fcm_token;
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/ervill-2017/messages:send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $msg ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        $gcmResult = json_decode($result);

        // dd($gcmResult);

        return $gcmResult;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
