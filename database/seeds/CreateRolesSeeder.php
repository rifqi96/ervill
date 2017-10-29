<?php

use Illuminate\Database\Seeder;

class CreateRolesSeeder extends Seeder
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
            'name'=>'owner'
        ));

        array_push($data, array(
            'id'=>2,
            'name'=>'admin'
        ));

        array_push($data, array(
            'id'=>3,
            'name'=>'driver'
        ));

        DB::table('roles')->insert($data);
    }
}
