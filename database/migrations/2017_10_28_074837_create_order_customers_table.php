<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('shipment_id')->unsigned()->nullable();
            $table->string('customer_name');
            $table->string('customer_address');
            $table->integer('empty_gallon_quantity');
            

            $table->foreign('order_id')->references('id')->on('orders')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shipment_id')->references('id')->on('shipments')
                ->onUpdate('cascade')->onDelete('null');
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
            $table->dropForeign('order_customers_order_id_foreign');
            $table->dropForeign('order_customers_shipment_id_foreign');
        });
        Schema::dropIfExists('order_customers');
    }
}
