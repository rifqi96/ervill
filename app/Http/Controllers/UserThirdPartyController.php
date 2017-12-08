<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserThirdParty;

class UserThirdPartyController extends Controller
{
    public function send_fcm_notification($data) {
        $third_party = UserThirdParty::where('user_id', $data->user_id)->first();

        if($third_party) {
            return $third_party->send_fcm_notification($data->message);
        }
        return false;

    }
}
