<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        $this->call('CreateRolesSeeder');
        $this->command->info("Roles table seeded :)");

        $this->call('CreateModulesSeeder');
        $this->command->info("Modules table seeded :)");

        $this->call('CreateModuleAccessesSeeder');
        $this->command->info("Module_Accesses table seeded :)");

        $this->call('CreateUsersSeeder');
        $this->command->info("Users table seeded :)");

        $this->call('CreateOutsourcingsSeeder');
        $this->command->info("Outsourcings table seeded :)");
    }
}
