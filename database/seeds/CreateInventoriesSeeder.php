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
            'name'=>'Galon Kosong',
            'quantity' => 0,
            'price' => 43000
        ));

        array_push($data, array(
            'id'=>2,
            'name'=>'Galon Isi',
            'quantity' => 0,
            'price' => 8000
        ));

        array_push($data, array(
            'id'=>3,
            'name'=>'Galon Rusak',
            'quantity' => 0,
            'price' => 10000
        ));

        foreach($data as $key=>$val){
            Inventory::create($data[$key]);
        }
    }
}
