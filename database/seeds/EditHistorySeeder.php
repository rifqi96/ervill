<?php

use Illuminate\Database\Seeder;
use App\Models\EditHistory;

class EditHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oc_edits = EditHistory::where('module_name', '=', 'Order Customer')->get();

        foreach($oc_edits as $oc_edit){
            $oc_edit->delete();
        }
    }
}
