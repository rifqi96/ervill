<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $data = array();
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->data['module'] = 'order';
    }

    public function getData(){
        return $this->data;
    }

    public function setData($key, $val){
        $this->data[$key] = $val;
    }
}
