<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;

class UserController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'user_management';
    }

     public function index()
    {
        $this->data['breadcrumb'] = "Setting - User Management";
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

    public function doUpdate(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'username' => 'required|string|min:3|unique:users,username,'.$user->id,
            'full_name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|string|digits_between:3,14'
        ]);   

        if($user->doUpdate($request)){
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

    public function getUsers()
    {
        $users = User::with('role')->get();
        return json_encode($users);
    }
    
}
