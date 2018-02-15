<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAllUnnecessaryTablesAndColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_customers', function (Blueprint $table){
            $table->dropForeign('order_customers_order_id_foreign');
            $table->dropForeign('order_customers_customer_id_foreign');

           $table->dropColumn('order_id');
           $table->dropColumn('customer_id');
           $table->dropColumn('empty_gallon_quantity');
           $table->dropColumn('additional_quantity');
           $table->dropColumn('is_new');
           $table->dropColumn('purchase_type');
           $table->dropColumn('delivery_at');
        });

        Schema::dropIfExists('order_customer_buy_invoices');
        Schema::dropIfExists('order_customer_buys');
        Schema::dropIfExists('order_customer_invoices');
        Schema::dropIfExists('customer_gallons');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
