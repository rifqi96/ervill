<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCustomerReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_customer_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->integer('filled_gallon_quantity');
            $table->integer('empty_gallon_quantity');
            $table->string('description');
            $table->string('status');
            $table->datetime('return_at')->nullable();
            $table->integer('author_id')->unsigned()->nullable();
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
        Schema::table('order_customer_returns', function (Blueprint $table){
            $table->dropForeign('order_customer_returns_customer_id_foreign');
            $table->dropForeign('order_customer_returns_author_id_foreign');
        });
        Schema::dropIfExists('order_customer_returns');
    }
}
