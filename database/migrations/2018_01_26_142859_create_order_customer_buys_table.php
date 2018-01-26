<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCustomerBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_customer_buys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->string('no_struk')->nullable();
            $table->integer('quantity');
            $table->integer('author_id')->unsigned()->nullable();
            $table->datetime('buy_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_customer_buys', function (Blueprint $table){
            $table->dropForeign('order_customer_buys_customer_id_foreign');
            $table->dropForeign('order_customer_buys_author_id_foreign');
        });
        Schema::dropIfExists('order_customer_buys');
    }
}
