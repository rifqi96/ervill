<?php

use Illuminate\Database\Seeder;

class CreateModulesSeeder extends Seeder
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
            'name'=>'overview'
        ));

        array_push($data, array(
            'id'=>2,
            'name'=>'gallon_make_order'
        ));

        array_push($data, array(
            'id'=>3,
            'name'=>'gallon_view_order'
        ));

        array_push($data, array(
            'id'=>4,
            'name'=>'gallon_inventory'
        ));

        array_push($data, array(
            'id'=>5,
            'name'=>'water_make_order'
        ));

        array_push($data, array(
            'id'=>6,
            'name'=>'water_view_order'
        ));

        array_push($data, array(
            'id'=>7,
            'name'=>'water_make_issue'
        ));

        array_push($data, array(
            'id'=>8,
            'name'=>'customer_make_order'
        ));

        array_push($data, array(
            'id'=>9,
            'name'=>'customer_view_order'
        ));

        array_push($data, array(
            'id'=>10,
            'name'=>'customer_tracking'
        ));

        array_push($data, array(
            'id'=>11,
            'name'=>'customer_make_issue'
        ));

        array_push($data, array(
            'id'=>12,
            'name'=>'settings_outsourcing'
        ));

        array_push($data, array(
            'id'=>13,
            'name'=>'settings_user_management'
        ));

        array_push($data, array(
            'id'=>14,
            'name'=>'settings_user_role'
        ));

        array_push($data, array(
            'id'=>15,
            'name'=>'settings_module_access'
        ));

        array_push($data, array(
            'id'=>16,
            'name'=>'settings_profile'
        ));

        DB::table('modules')->insert($data);
    }
}
