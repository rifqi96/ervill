<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentDateIntoOcHeaderInvoiceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_header_invoices', function (Blueprint $table) {
            $table->datetime('payment_date')->nullable()->after('is_free');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oc_header_invoices', function (Blueprint $table) {
            $table->dropColumn('payment_date');
        });
    }
}
