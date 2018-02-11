<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCustomerInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_customer_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('oc_header_invoice_id');
            $table->integer('order_customer_id')->unsigned()->nullable();
            $table->integer('quantity');
            $table->integer('subtotal');
            $table->timestamps();

            $table->foreign('oc_header_invoice_id')->references('id')->on('oc_header_invoices')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('order_customer_id')->references('id')->on('order_customers')
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
        Schema::dropIfExists('order_customer_invoices');
    }
}
