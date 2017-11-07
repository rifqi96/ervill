<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveDeliveryAtInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_at');
        });

        Schema::table('order_waters', function (Blueprint $table) {
            $table->datetime('delivery_at')->nullable()->after('driver_name');
        });
        Schema::table('order_customers', function (Blueprint $table) {
            $table->datetime('delivery_at')->nullable()->after('empty_gallon_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_waters', function (Blueprint $table) {
            $table->dropColumn('delivery_at');
        });
        Schema::table('order_customers', function (Blueprint $table) {
            $table->dropColumn('delivery_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->datetime('delivery_at')->nullable()->after('created_at');
        });
    }
}
