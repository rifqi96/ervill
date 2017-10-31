<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderWaterIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_water_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_water_id')->unsigned();
            $table->integer('issue_id')->unsigned();
            

            $table->foreign('order_water_id')->references('id')->on('order_waters')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('issue_id')->references('id')->on('issues')
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
        Schema::table('order_water_issues', function (Blueprint $table) {
            $table->dropForeign('order_water_issues_order_water_id_foreign');
            $table->dropForeign('order_water_issues_issue_id_foreign');
        });
        Schema::dropIfExists('order_water_issues');
    }
}
