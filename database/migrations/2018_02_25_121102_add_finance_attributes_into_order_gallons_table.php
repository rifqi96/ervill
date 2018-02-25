<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFinanceAttributesIntoOrderGallonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_gallons', function (Blueprint $table) {
            $table->string('purchase_invoice_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->integer('price')->nullable();
            $table->integer('total')->nullable();
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
            $table->dropColumn(['purchase_invoice_no','invoice_no','price','total']);
        });
    }
}
