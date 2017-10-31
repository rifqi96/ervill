<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderGallonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_gallons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('outsourcing_id')->unsigned();
            

            $table->foreign('order_id')->references('id')->on('orders')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('outsourcing_id')->references('id')->on('outsourcings')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_gallons', function (Blueprint $table) {
            $table->dropForeign('order_gallons_order_id_foreign');
            $table->dropForeign('order_gallons_outsourcing_id_foreign');
        });
        Schema::dropIfExists('order_gallons');
    }
}
