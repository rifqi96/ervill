<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCustomerIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_customer_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_customer_id')->unsigned();
            $table->integer('issue_id')->unsigned();
            

            $table->foreign('order_customer_id')->references('id')->on('order_customers')
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
        Schema::dropIfExists('order_customer_issues');
    }
}
