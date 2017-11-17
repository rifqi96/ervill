<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToEditHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('edit_histories', function(Blueprint $table){
            $table->integer('user_id')->length(10)->unsigned()->after('description');

            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::table('edit_histories', function(Blueprint $table){
            $table->dropForeign('edit_histories_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
