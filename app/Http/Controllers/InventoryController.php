<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\EditHistory;

class InventoryController extends Controller
{
	public function __construct()
    {
        $this->middleware('SuperadminAndAdmin');
        $this->data['module'] = 'inventory';
        $this->data['slug'] = '';
    }

    public function index(){
        $this->data['breadcrumb'] = 'Inventory';

        return view('inventory', $this->data);

    }

    public function getInventories()
    {
        $inventories = Inventory::all();
        return json_encode($inventories);
    }

    public function doUpdate(Request $request)
    {
        $this->middleware('superadmin');

        $this->validate($request, [           
            'quantity' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);   
        
        $inventory = Inventory::select('id','quantity','price')->find($request->id);
        
        //set old values
        $old_value_obj = $inventory->toArray();
      	unset($old_value_obj['id']);
        $old_value = '';
        $i=0;
        foreach ($old_value_obj as $row) { 
            if($i == count($old_value_obj)-1){                
                $old_value .= $row;
            }else{
                $old_value .= $row.';';
            }           
            $i++;
        }

        //set new values
        $new_value_obj = $request->toArray();
  
        unset($new_value_obj['id']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);
        $new_value = '';
        $i=0;
        foreach ($new_value_obj as $row) {
            if($i == count($new_value_obj)-1){
                $new_value .= $row;
            }else{
                $new_value .= $row.';';
            }
            $i++;
        }
        
        $edit_data = array(
            'module_name' => 'Inventory',
            'data_id' => $request->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description,
            'user_id' => auth()->id()
        );

        if($inventory->doUpdate($request) && EditHistory::create($edit_data)){
            //dd($inventory->id . $request->id);
            return back()
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }
}
