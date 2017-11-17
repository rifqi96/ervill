<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutsourcingWater;
use App\Models\OutsourcingDriver;
use App\Models\EditHistory;

class OutsourcingController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'outsourcing';
    }

     public function index()
    {
        $this->data['breadcrumb'] = "Setting - Outsourcing";

        return view('setting.outsourcing.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Setting - Outsourcing - Create";

        return view('setting.outsourcing.make', $this->data);
    }

    public function doMake(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|integer|between:1,2',
            'name' => 'required|string|min:3'           
        ]);   

        if($request->type==1){
            $outsourcingDriver = new OutsourcingDriver();
            if($outsourcingDriver->doMake($request)){
                return back();
            }else{
                return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
            }
            
        }else if($request->type==2){
            $outsourcingWaters = new OutsourcingWater();
            if($outsourcingWaters->doMake($request)){
                return back();
            }else{
                return back()
                ->withErrors(['message' => 'There is something wrong, please contact admin']);
            }
        }

        return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
    }

    public function doUpdateWater(Request $request)
    {
        $this->validate($request, [           
            'name' => 'required|string',           
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);   

        $outsourcingWaters = OutsourcingWater::find($request->id);

        //set old values
        $old_value_obj = OutsourcingWater::where('id',$request->id)->first()->toArray();
        unset($old_value_obj['created_at']);
        unset($old_value_obj['updated_at']);
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
            'module_name' => 'Outsourcing Water',
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description
        );

        if($outsourcingWaters->doUpdate($request) && EditHistory::create($edit_data)){
            return back();
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doUpdateDriver(Request $request)
    {
        $this->validate($request, [           
            'name' => 'required|string',           
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);   

        $outsourcingDrivers = OutsourcingDriver::find($request->id);

        //set old values
        $old_value_obj = OutsourcingDriver::where('id',$request->id)->first()->toArray();
        unset($old_value_obj['created_at']);
        unset($old_value_obj['updated_at']);
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
            'module_name' => 'Outsourcing Driver',
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description
        );

        if($outsourcingDrivers->doUpdate($request) && EditHistory::create($edit_data)){
            return back();
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function getOutsourcingWaters()
    {
        $outsourcingWaters = OutsourcingWater::all();
        return json_encode($outsourcingWaters);
    }

    public function getOutsourcingDrivers()
    {
        $outsourcingDrivers = OutsourcingDriver::all();
        return json_encode($outsourcingDrivers);
    }

}
