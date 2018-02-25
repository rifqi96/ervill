<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionAndAdditionalPriceToOcHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oc_header_invoices', function (Blueprint $table){
            $table->integer('additional_price')->nullable()->after('delivery_at');
            $table->string('description')->nullable()->after('delivery_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oc_header_invoices', function (Blueprint $table){
            $table->dropColumn('additional_price');
            $table->dropColumn('description');
        });
    }
}
