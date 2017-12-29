<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOutsourcingWatersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_waters', function (Blueprint $table) {
            $table->dropForeign('order_waters_outsourcing_water_id_foreign');
            $table->dropColumn('outsourcing_water_id');
        });

        Schema::dropIfExists('outsourcing_waters');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('outsourcing_waters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('order_waters', function (Blueprint $table) {
            $table->integer('outsourcing_water_id')->unsigned()->nullable()->after('id');

            $table->foreign('outsourcing_water_id')->references('id')->on('outsourcing_waters')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }
}
