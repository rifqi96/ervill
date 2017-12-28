<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('order_customers', function (Blueprint $table){
            $table->dropColumn('customer_name');
            $table->dropColumn('customer_address');
            $table->integer('customer_id')->unsigned()->nullable()->after('shipment_id');

            $table->foreign('customer_id')->references('id')->on('customers')
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
        Schema::table('order_customers', function (Blueprint $table){
            $table->dropForeign('order_customers_customer_id_foreign');
            $table->dropColumn('customer_id');
            $table->string('customer_name');
            $table->string('customer_address');
        });

        Schema::dropIfExists('customers');
    }
}
