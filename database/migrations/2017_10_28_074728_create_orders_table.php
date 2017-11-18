<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('inventory_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('quantity');
            $table->datetime('delivery_at')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('inventory_id')->references('id')->on('inventories')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_inventory_id_foreign');
            $table->dropForeign('orders_user_id_foreign');
        });
        Schema::dropIfExists('orders');
    }
}
