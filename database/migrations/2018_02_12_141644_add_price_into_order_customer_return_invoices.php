<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceIntoOrderCustomerReturnInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_customer_return_invoices', function (Blueprint $table) {
            $table->integer('price_number')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_customer_return_invoices', function (Blueprint $table) {
            $table->dropColumn('price_number');
        });
    }
}
