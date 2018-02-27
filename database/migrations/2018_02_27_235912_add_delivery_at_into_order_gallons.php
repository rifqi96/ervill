<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryAtIntoOrderGallons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_gallons', function (Blueprint $table) {
            $table->datetime('delivery_at')->nullable()->after('driver_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_gallons', function (Blueprint $table) {
            $table->dropColumn('delivery_at');
        });
    }
}
