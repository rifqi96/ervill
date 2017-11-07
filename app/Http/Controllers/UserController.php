<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        return view('setting.user_management.make', $this->data);
    }

    public function showProfile()
    {
        $this->data['module'] = '';
        $this->data['slug'] = '';
        $this->data['breadcrumb'] = "Profile";

        return view('profile', $this->data);
    }
}
