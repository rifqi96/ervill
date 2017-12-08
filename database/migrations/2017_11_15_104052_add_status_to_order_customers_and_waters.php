<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToOrderCustomersAndWaters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_customers', function (Blueprint $table){
            $table->string('status');
        });

        Schema::table('order_waters', function (Blueprint $table){
            $table->string('status');
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
            $table->dropColumn('status');
        });

        Schema::table('order_waters', function (Blueprint $table){
            $table->dropColumn('status');
        });
    }
}
