<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCustomerNonErvillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_customer_non_ervills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ne_header_invoice_id')->nullable();
            $table->string('name');
            $table->integer('quantity');
            $table->integer('price_id')->unsigned()->nullable();
            $table->integer('price');
            $table->integer('subtotal');
            $table->timestamps();

            $table->foreign('ne_header_invoice_id')->references('id')->on('ne_header_invoices')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('price_id')->references('id')->on('prices')
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

        Schema::table('order_customer_non_ervills', function (Blueprint $table) {
            $table->dropForeign('order_customer_non_ervills_ne_header_invoice_id_foreign','order_customer_non_ervills_price_id_foreign');
        });
        Schema::dropIfExists('order_customer_non_ervills');
    }
}
