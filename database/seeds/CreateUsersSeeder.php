<?php

use Illuminate\Database\Seeder;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array();

        for($i=1; $i<=3; $i++){
            if($i == 1){
                array_push($data, array(
                    'id' => $i,
                    'username' => 'owner',
                    'password' => bcrypt('owner'),
                    'full_name' => 'Sulhan Syadeli',
                    'email' => 'owner@ervill.com',
                    'phone' => '081314151818'
                ));
            }
            else if($i == 2){
                array_push($data, array(
                    'id' => $i,
                    'username' => 'admin',
                    'password' => bcrypt('admin'),
                    'full_name' => 'Ervill Admin',
                    'email' => 'admin@ervill.com',
                    'phone' => '08129380921'
                ));
            }
            else if($i == 3){
                array_push($data, array(
                    'id' => $i,
                    'username' => 'driver',
                    'password' => bcrypt('driver'),
                    'full_name' => 'Ervill Driver',
                    'phone' => '085882738190'
                ));
            }
        }

        DB::table('users')->insert($data);
    }
}
