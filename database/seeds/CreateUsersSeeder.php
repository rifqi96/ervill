<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array();

        foreach(Role::all() as $role){
            if($role->name == "superadmin"){
                $roles['superadmin'] = $role->id;
            }
            else if($role->name == "admin"){
                $roles['admin'] = $role->id;
            }
            else if($role->name == "driver"){
                $roles['driver'] = $role->id;
            }
        }

        $data = array();

        for($i=1; $i<=3; $i++){
            if($i == 1){
                array_push($data, array(
                    'id' => $i,
                    'role_id' => $roles['superadmin'],
                    'username' => 'superadmin',
                    'password' => bcrypt('superadmin'),
                    'full_name' => 'Sulhan Syadeli',
                    'email' => 'owner@ervill.net',
                    'phone' => '081314151818'
                ));
            }
            else if($i == 2){
                array_push($data, array(
                    'id' => $i,
                    'role_id' => $roles['admin'],
                    'username' => 'admin',
                    'password' => bcrypt('admin'),
                    'full_name' => 'Ervill Admin',
                    'email' => 'admin@ervill.net',
                    'phone' => '08129380921'
                ));
            }
            else if($i == 3){
                array_push($data, array(
                    'id' => $i,
                    'role_id' => $roles['driver'],
                    'username' => 'driver',
                    'password' => bcrypt('driver'),
                    'full_name' => 'Ervill Driver',
                    'email' => 'driver@ervill.net',
                    'phone' => '085882738190'
                ));
            }
        }

        foreach($data as $key=>$val){
            User::create($data[$key]);
        }

//        DB::table('users')->insert($data);
    }
}
