<?php

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Price;

class AddOrderNonErvill extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Inventory::create(array(
            'id'=>8,
            'name'=>'Galon Terjual Non Ervill Aqua',
            'quantity' => 0,
            'price' => 25000
        ));
        Inventory::create(array(
            'id'=>9,
            'name'=>'Galon Terjual Non Ervill Non Aqua',
            'quantity' => 0,
            'price' => 20000
        ));

        Price::create(array(
            'id'=>15,
            'name'=>'Jual galon Non Ervill merk Aqua',
            'customer_type' => 'third_party',
            'price' => 25000
        ));
        Price::create(array(
            'id'=>16,
            'name'=>'Jual galon Non Ervill merk NON Aqua',
            'customer_type' => 'third_party',
            'price' => 20000
        ));
    }
}
