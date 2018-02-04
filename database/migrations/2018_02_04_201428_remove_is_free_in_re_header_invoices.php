<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveIsFreeInReHeaderInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_header_invoices', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_header_invoices', function (Blueprint $table) {
            $table->string('is_free')->after('payment_status');
        });
    }
}
