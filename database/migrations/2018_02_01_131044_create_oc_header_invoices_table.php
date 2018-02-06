<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOCHeaderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oc_header_invoices', function (Blueprint $table) {
            $table->string('id');
            $table->string('payment_status');
            $table->string('is_free');
            $table->integer('shipment_id')->unsigned()->nullable();
            $table->timestamps();

            $table->primary('id');

            $table->foreign('shipment_id')->references('id')->on('shipments')
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
        Schema::table('oc_header_invoices', function (Blueprint $table) {
            $table->dropForeign('oc_header_invoices_shipment_id_foreign');
        });
        Schema::dropIfExists('oc_header_invoices');
    }
}
