<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Inventory;

class IssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('SuperadminAndAdmin');
    }

    public function doDelete(Request $request){
    	$issue = Issue::find($request->id);

        if( $issue->doDelete() ){
            return 'Data telah berhasil dihapus';
        }else{
            return 'There is something wrong, please contact admin';
        }
    }
}
