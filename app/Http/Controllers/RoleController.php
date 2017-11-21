<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->middleware('superadmin');
        $this->data['slug'] = 'user_role';
    }

     public function index()
    {
        $this->data['breadcrumb'] = "Setting - User Role";

        return view('setting.user_role.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Setting - User Role - Create";

        return view('setting.user_role.make', $this->data);
    }
}
