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
        $this->middleware('SuperadminAndAdmin');
        $this->data['slug'] = 'user_management';
    }

    /*======= Page Methods =======*/
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

    public function showDetails($id)
    {
        $this->data['breadcrumb'] = "Setting - User Management - User Details";

        $this->data['user'] = $this->get($id);

        return view('setting.user_management.details', $this->data);
    }

    public function showProfile()
    {
        $this->data['module'] = '';
        $this->data['slug'] = '';
        $this->data['breadcrumb'] = "Profile";

        return view('profile', $this->data);
    }

    /*======= Get Methods =======*/
    public function getUsers()
    {
        return User::with('role')->get();
    }

    public function getAllDrivers(){
        return User::with('role')
            ->whereHas('role', function($query){
                $query->where('name', 'driver');
            })
            ->get();
    }

    public function get($id){
        return User::with('role')->find($id);

    }

    /*======= Do Methods =======*/
    public function doUpdateProfile(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'username' => 'required|string|min:3|unique:users,username,'.$user->id,           
            'full_name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string|digits_between:3,14'
        ]);   

        if($request->change_password){
            $this->validate($request, [                
                'password' => 'required|string|min:6|confirmed',                
            ]);  
        }

        if($user->doUpdateProfile($request)){
            return back()
            ->with('success', 'Data telah berhasil diupdate');
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
            return back()
            ->with('success', 'Data telah berhasil dibuat');
        }else{
            return back()
            ->withErrors(['message' => 'There is something wrong, please contact admin']);
        }
    }

    public function doUpdate(Request $request)
    {
        $user = User::with('role')->select('id','role_id','username','full_name','email','phone')->find($request->id);
        
        $this->validate($request, [
            'role' => 'required|integer|exists:roles,id',
            'username' => 'required|string|min:3|unique:users,username,'.$request->id,
            'full_name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string|digits_between:3,14',
            'description' => 'required|string|regex:/^[^;]+$/'
        ]);   

        if($request->change_password){
            $this->validate($request, [                
                'password' => 'required|string|min:6|confirmed',                
            ]);  
        }
        

        //set old values
        $old_value_obj = $user->toArray();
        unset($old_value_obj['id']);        
        unset($old_value_obj['role_id']);
        $old_value = '';
        $i=0;
        foreach ($old_value_obj as $row) { 
            if($i == count($old_value_obj)-1){                
                $old_value .= $row['name'];
            }else{
                $old_value .= $row.';';
            }           
            $i++;
        }


        //set new values
        $new_value_obj = $request->toArray();
        $new_value_obj['role_name'] = Role::find($new_value_obj['role'])->name;
        unset($new_value_obj['id']);
        unset($new_value_obj['role']);
        unset($new_value_obj['_token']);
        unset($new_value_obj['description']);        
        unset($new_value_obj['password_confirmation']);
        unset($new_value_obj['change_password']);
        if(!$request->change_password){
            unset($new_value_obj['password']);
        }
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
            'data_id' => $request->id,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'description' => $request->description,
            'user_id' => auth()->id()
        );

        if($user->doUpdate($request) && EditHistory::create($edit_data)){
            return back()
            ->with('success', 'Data telah berhasil diupdate');
        }else{
            return back()
            ->withErrors(['message' => 'Terjadi kesalahan pada update data']);
        }
    }

    public function doDelete(Request $request){
        $user = User::find($request->user_id);

        $this->validate($request, [
            'description' => 'required|string|regex:/^[^;]+$/'
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
    
}
