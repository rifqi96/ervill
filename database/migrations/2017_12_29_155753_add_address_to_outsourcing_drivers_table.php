<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressToOutsourcingDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outsourcing_drivers', function(Blueprint $table) {
            $table->string('address')->after('name');
            $table->string('phone')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outsourcing_drivers', function(Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('phone');
        });
    }
}
