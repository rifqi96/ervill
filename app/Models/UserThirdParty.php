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
        $APIKEY = "AAAA_O6I6XM:APA91bGySAqR_UWRhDkIpA1hqXim7qeiTG8IxE9vi0Q2xrHVxZttZirI7speBUrmOZac6X9xXRyvb0uL9vlZoCEZoZ8o_20-VK9fAQzM3vstwsKimsK18B8UvD1JIm2yliplZYmLozBx";

        $headers = array(
            'Content-Type: application/json',
            'Authorization: key='.$APIKEY
        );

        $msg['token'] = $this->fcm_token;
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $msg ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        $gcmResult = json_decode($result);

        return $gcmResult;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
