<?php

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class CreateInventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array();

        array_push($data, array(
            'id'=>1,
            'name'=>'Galon Kosong Buffer',
            'quantity' => 0,
            'price' => 43000
        ));

        array_push($data, array(
            'id'=>2,
            'name'=>'Galon Kosong Gudang',
            'quantity' => 0,
            'price' => 43000
        ));

        array_push($data, array(
            'id'=>3,
            'name'=>'Galon Isi',
            'quantity' => 0,
            'price' => 51000
        ));

        array_push($data, array(
            'id'=>4,
            'name'=>'Galon Rusak',
            'quantity' => 0,
            'price' => 33000
        ));

        array_push($data, array(
            'id'=>5,
            'name'=>'Galon Beredar',
            'quantity' => 0,
            'price' => 43000
        ));

        array_push($data, array(
            'id'=>6,
            'name'=>'Galon Non Ervill',
            'quantity' => 0,
            'price' => 25000
        ));

        foreach($data as $key=>$val){
            Inventory::create($data[$key]);
        }
    }
}
