<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsNonRefundIntoOrderCustomerReturns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_customer_returns', function (Blueprint $table) {
            $table->string('is_non_refund')->after('empty_gallon_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_customer_returns', function (Blueprint $table) {
            $table->dropColumn('is_non_refund');
        });
    }
}
