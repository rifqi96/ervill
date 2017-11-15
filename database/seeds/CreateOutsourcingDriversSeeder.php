<?php

use Illuminate\Database\Seeder;
use App\Models\OutsourcingDriver;

class CreateOutsourcingDriversSeeder extends Seeder
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
            'id' => 1,
            'name' => 'Ervill\'s Driver'
        ));

        foreach($data as $key=>$val){
            OutsourcingDriver::create($data[$key]);
        }
    }
}
