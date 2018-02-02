<?php

use Illuminate\Database\Seeder;
use App\Models\Price;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array();

        // End Customer //
        array_push($data, array(
            'id'=>1,
            'name'=>'Isi ulang air',
            'customer_type' => 'end_customer',
            'price' => 12000
        ));

        array_push($data, array(
            'id'=>2,
            'name'=>'Pinjam galon',
            'customer_type' => 'end_customer',
            'price' => 12000
        ));

        array_push($data, array(
            'id'=>3,
            'name'=>'Tukar galon merk lain',
            'customer_type' => 'end_customer',
            'price' => 12000
        ));

        array_push($data, array(
            'id'=>4,
            'name'=>'Beli galon',
            'customer_type' => 'end_customer',
            'price' => 42000
        ));

        array_push($data, array(
            'id'=>5,
            'name'=>'Retur galon kosong',
            'customer_type' => 'end_customer',
            'price' => 30000
        ));

        array_push($data, array(
            'id'=>6,
            'name'=>'Retur galon isi',
            'customer_type' => 'end_customer',
            'price' => 42000
        ));

        // Agen //
        array_push($data, array(
            'id'=>7,
            'name'=>'Isi ulang air',
            'customer_type' => 'agent',
            'price' => 10000
        ));

        array_push($data, array(
            'id'=>8,
            'name'=>'Pinjam galon',
            'customer_type' => 'agent',
            'price' => 10000
        ));

        array_push($data, array(
            'id'=>9,
            'name'=>'Tukar galon merk lain',
            'customer_type' => 'agent',
            'price' => 10000
        ));

        array_push($data, array(
            'id'=>10,
            'name'=>'Beli galon',
            'customer_type' => 'agent',
            'price' => 40000
        ));

        array_push($data, array(
            'id'=>11,
            'name'=>'Retur galon kosong',
            'customer_type' => 'agent',
            'price' => 30000
        ));

        array_push($data, array(
            'id'=>12,
            'name'=>'Retur galon isi',
            'customer_type' => 'agent',
            'price' => 40000
        ));

        foreach($data as $key=>$val){
            Price::create($data[$key]);
        }
    }
}
