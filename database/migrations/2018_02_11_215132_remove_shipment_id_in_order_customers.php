<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveShipmentIdInOrderCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_customers', function (Blueprint $table) {
            $table->dropForeign('order_customers_shipment_id_foreign');
            $table->dropColumn('shipment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_customers', function (Blueprint $table) {
            $table->integer('shipment_id')->unsigned()->nullable()->after('order_id');
            $table->foreign('shipment_id')->references('id')->on('shipments')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }
}
