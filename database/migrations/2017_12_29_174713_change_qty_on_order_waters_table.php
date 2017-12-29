<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeQtyOnOrderWatersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_waters', function (Blueprint $table) {
            $table->integer('buffer_qty')->after('order_id');
            $table->integer('warehouse_qty')->after('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_waters', function (Blueprint $table) {
            $table->dropColumn('buffer_qty');
            $table->dropColumn('warehouse_qty');
        });
    }
}
