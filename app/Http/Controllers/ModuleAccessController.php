<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleAccessController extends SettingController
{
    public function __construct(){
        parent::__construct();
        $this->data['slug'] = 'module_access';
    }

     public function index()
    {
        $this->data['breadcrumb'] = "Setting - Module Access";

        return view('setting.module_access.index', $this->data);
    }

    public function showMake()
    {
        $this->data['breadcrumb'] = "Setting - Module Access - Create";

        return view('setting.module_access.make', $this->data);
    }
}
