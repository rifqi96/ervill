<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        return new UserResource(User::find($id));
    }
    public function index()
    {
        return UserResource::collection(User::all());
    }
    public function store(Request $request)
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
        
        if($user->doMakeByPass($request)){
            return $user;
        }else{
            return 'error creating user';
        }
    }
}
