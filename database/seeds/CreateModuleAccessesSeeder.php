<?php

use Illuminate\Database\Seeder;
use App\Models\Module;

class CreateModuleAccessesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array();

        $modules = Module::all();

        foreach($modules as $module){
            switch($module->id){
                // Overview //
                case 1:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Gallon: Make Order //
                case 2:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Gallon: View Order //
                case 3:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Gallon: Inventory //
                case 4:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Water: Make Order //
                case 5:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Water: View Order //
                case 6:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Water: Make Issue //
                case 7:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Customer: Make Order //
                case 8:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Customer: Customer View Order //
                case 9:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Customer: Tracking //
                case 10:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Customer: Make Issue //
                case 11:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 3
                    ));
                    break;

                // Settings: Outsourcing //
                case 12:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));
                    break;

                // Settings: User Management //
                case 13:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));
                    break;

                // Settings: User Roles //
                case 14:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));
                    break;

                // Settings: Module Access //
                case 15:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));
                    break;

                // Settings: Profile //
                case 16:
                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 1
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 2
                    ));

                    array_push($data, array(
                        'module_id' => $module->id,
                        'role_id' => 3
                    ));
                    break;
            }
        }

        DB::table('module_accesses')->insert($data);
    }
}
