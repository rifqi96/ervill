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

    	$empty_gallon = Inventory::find(1);
        $filled_gallon = Inventory::find(2);
        $broken_gallon = Inventory::find(3);

    	if($issue->type=="Kesalahan Pabrik Air"){

    	}

        if( $issue->delete() ){
            return 'Data telah berhasil dihapus';
        }else{
            return 'There is something wrong, please contact admin';
        }
    }
}
