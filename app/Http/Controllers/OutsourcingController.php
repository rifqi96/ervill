<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
