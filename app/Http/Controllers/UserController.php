<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\EditHistory;
use App\Models\DeleteHistory;

class UserController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'user_management';
    }

     public function index()
    {
        $this->data['breadcrumb'] = "Setting - User Management";

        if(auth()->user()->role->name == 'superadmin')
            $this->data['roles'] = Role::all();
        else if(auth()->user()->role->name == 'admin')
            $this->data['role'] = Role::where('name','driver')->first();

        return view('setting.user_management.index', $this->data);
    }

    public function showMake()
    {    
        $this->data['breadcrumb'] = "Setting - User Management - Create";

        if(auth()->user()->role->name == 'superadmin')
            $this->data['roles'] = Role::all();
        else if(auth()->user()->role->name == 'admin')
            $this->data['roles'] = Role::where('name','driver')->get();

        return view('setting.user_management.make', $this->data);
        

    }

    public function showProfile()
    {
        $this->data['module'] = '';
        $this->data['slug'] = '';
        $this->data['breadcrumb'] = "Profile";

        return view('profile', $this->data);
    }

    public function doUpdateProfile(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'username' => 'required|string|min:3|unique:users,username,'.$user->id,
            'full_name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string|digits_between:3,14'
        ]);   

        if($user->doUpdateProfile($request)){
            return back();
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doMake(Request $request)
    {
        $this->validate($request, [
            'role' => 'required|integer|exists:roles,id',
            'username' => 'required|string|min:3|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string|digits_between:3,14'
        ]);   

        $user = new User();
        
        if($user->doMake($request)){
            return back();
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doUpdate(Request $request)
    {
        $user = User::find($request->id);

        $this->validate($request, [
            'role' => 'required|integer|exists:roles,id',
            'username' => 'required|string|min:3|unique:users,username,'.$user->id,
            'full_name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string|digits_between:3,14',
            'description' => 'required|string'
        ]);   

        

        //set old values
        $old_value_obj = User::where('id',$request->id)->first()->toArray();
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
            'module_name' => 'User Management',
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description
        );

        if($user->doUpdate($request) && EditHistory::create($edit_data)){
            return back();
        }else{
            return back()
            ->withErrors(['message' => 'Terjadi kesalahan pada update data']);
        }
    }

    public function doDelete(Request $request){
        $user = User::find($request->user_id);

        $this->validate($request, [
            'description' => 'required|string'
        ]);

        $data = array(
            'module_name' => 'User Management',
            'description' => $request->description,
            'data_id' => $user->id,
            'user_id' => auth()->user()->id
        );

        if(auth()->user()->role->name=='admin' && $user->role!=3){
            return back()
                ->withErrors(['message' => 'Admin hanya dapat update data driver']);
        }

        if($user->doDelete() && DeleteHistory::create($data)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }

    public function doForceDelete(Request $request){
        $user = User::onlyTrashed()->find($request->user_id);

        if(auth()->user()->role->name!='superadmin'){
            return back()
                ->withErrors(['message' => 'Anda tidak dapat menghapus data secara permanen']);
        }

        if($user->doForceDelete() && DeleteHistory::destroy($request->data_id)){
            return back()
                ->with('success', 'Data telah berhasil dihapus');
        }else{
            return back()
                ->withErrors(['message' => 'Terjadi kesalahan pada penghapusan data']);
        }
    }

    public function getUsers()
    {
        $users = User::with('role')->get();
        return json_encode($users);
    }
    
}
