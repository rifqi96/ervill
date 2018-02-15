<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerGallonsIntoCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table){
            $table->integer('non_erv_qty')->nullable()->after('type');
            $table->integer('purchase_qty')->nullable()->after('type');
            $table->integer('rent_qty')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table){
            $table->dropColumn('rent_qty');
            $table->dropColumn('purchase_qty');
            $table->dropColumn('non_erv_qty');
        });
    }
}
