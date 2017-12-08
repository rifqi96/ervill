<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeleteHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delete_histories', function (Blueprint $table){
            $table->increments('id');
            $table->string('module_name');
            $table->string('description');
            $table->integer('data_id')->length(10)->unsigned();
            $table->integer('user_id')->length(10)->unsigned()->nullable();
            $table->timestamps();

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
        Schema::table('delete_histories', function(Blueprint $table){
            $table->dropForeign('delete_histories_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::dropIfExists('delete_histories');
    }
}
