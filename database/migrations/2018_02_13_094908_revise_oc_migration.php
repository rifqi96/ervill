<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ReviseOcMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_customers', function (Blueprint $table){
//            $table->integer('price_id')->nullable();
            $table->integer('subtotal')->nullable()->after('id');
            $table->integer('price')->nullable()->after('id');
            $table->integer('price_id')->nullable()->unsigned()->after('id');
            $table->integer('quantity')->nullable()->after('id');
            $table->string('name')->nullable()->after('id');
            $table->string('oc_header_invoice_id')->nullable()->after('id');

            $table->foreign('oc_header_invoice_id')->references('id')->on('oc_header_invoices')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('price_id')->references('id')->on('prices')
                ->onUpdate('cascade')->onDelete('set null');
        });

        Schema::table('oc_header_invoices', function (Blueprint $table){
            $table->timestamp('delivery_at')->nullable()->after('shipment_id');
            $table->timestamp('accepted_at')->nullable()->after('shipment_id');
            $table->integer('user_id')->nullable()->unsigned()->after('shipment_id');
            $table->integer('customer_id')->nullable()->unsigned()->after('shipment_id');
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')
                ->onUpdate('cascade')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::table('order_customers', function (Blueprint $table){
            $table->dropForeign('order_customers_oc_header_invoice_id_foreign');
            $table->dropForeign('order_customers_price_id_foreign');
            $table->dropColumn('oc_header_invoice_id');
            $table->dropColumn('name');
            $table->dropColumn('quantity');
            $table->dropColumn('price');
            $table->dropColumn('subtotal');
        });

        Schema::table('oc_header_invoices', function (Blueprint $table){
            $table->dropColumn('delivery_at');
            $table->dropForeign('oc_header_invoices_customer_id_foreign');
            $table->dropColumn('customer_id');
            $table->dropForeign('oc_header_invoices_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropColumn('deleted_at');
            $table->dropColumn('accepted_at');
        });
    }
}
