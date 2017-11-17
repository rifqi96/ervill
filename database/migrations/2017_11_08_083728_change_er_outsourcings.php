<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeErOutsourcings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_waters', function (Blueprint $table){
            $table->dropForeign('order_waters_outsourcing_id_foreign');
            $table->dropColumn('outsourcing_id');
        });

        Schema::table('order_gallons', function (Blueprint $table){
            $table->dropForeign('order_gallons_outsourcing_id_foreign');
            $table->dropColumn('outsourcing_id');
        });

        Schema::dropIfExists('outsourcings');

        Schema::create('outsourcing_drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('outsourcing_waters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('order_waters', function (Blueprint $table){
            $table->integer('outsourcing_water_id')->unsigned()->after('id');
            $table->integer('outsourcing_driver_id')->unsigned()->after('outsourcing_water_id');

            $table->foreign('outsourcing_water_id')->references('id')->on('outsourcing_waters')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('outsourcing_driver_id')->references('id')->on('outsourcing_drivers')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('order_gallons', function (Blueprint $table){
            $table->integer('outsourcing_driver_id')->unsigned()->after('id');

            $table->foreign('outsourcing_driver_id')->references('id')->on('outsourcing_drivers')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_gallons', function (Blueprint $table){
            $table->dropForeign('order_gallons_outsourcing_driver_id_foreign');
            $table->dropColumn('outsourcing_driver_id');
        });

        Schema::table('order_waters', function (Blueprint $table){
            $table->dropForeign('order_waters_outsourcing_driver_id_foreign');
            $table->dropColumn('outsourcing_driver_id');

            $table->dropForeign('order_waters_outsourcing_water_id_foreign');
            $table->dropColumn('outsourcing_water_id');
        });

        Schema::dropIfExists('outsourcing_waters');
        Schema::dropIfExists('outsourcing_drivers');

        Schema::create('outsourcings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('order_gallons', function (Blueprint $table){
            $table->integer('outsourcing_id')->unsigned()->after('id');
            $table->foreign('outsourcing_id')->references('id')->on('outsourcings')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('order_waters', function (Blueprint $table){
            $table->integer('outsourcing_id')->unsigned()->after('id');
            $table->foreign('outsourcing_id')->references('id')->on('outsourcings')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
