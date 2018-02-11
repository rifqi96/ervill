<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceIdIntoOrderCustomerBuyInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_customer_buy_invoices', function (Blueprint $table) {
            $table->integer('price_id')->unsigned()->nullable()->after('order_customer_buy_id');

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
        Schema::table('order_customer_buy_invoices', function (Blueprint $table) {
            $table->dropForeign('order_customer_buy_invoices_price_id_foreign');
            $table->dropColumn('price_id');
        });
    }
}
