<?php

use Illuminate\Database\Seeder;
use App\Models\Inventory;

class AddSoldGallonsToInventory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Inventory::create(array(
            'id'=>7,
            'name'=>'Galon Terjual',
            'quantity' => 0,
            'price' => 43000
        ));
    }
}
