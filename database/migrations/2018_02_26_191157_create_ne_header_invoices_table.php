<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNeHeaderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ne_header_invoices', function (Blueprint $table) {
            $table->string('id');
            $table->string('payment_status');    
            $table->datetime('payment_date')->nullable();
            $table->integer('customer_non_ervill_id')->unsigned()->nullable();   
            $table->integer('user_id')->unsigned()->nullable();  
            $table->datetime('accepted_at')->nullable();  
            $table->timestamp('delivery_at')->nullable(); 
            $table->string('description')->nullable();
            $table->integer('additional_price')->nullable();
            $table->string('status');          
            $table->timestamps();
           
            $table->softDeletes();
            $table->primary('id');

            $table->foreign('customer_non_ervill_id')->references('id')->on('customer_non_ervills')
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
        Schema::table('ne_header_invoices', function (Blueprint $table) {
            $table->dropForeign('ne_header_invoices_customer_non_ervill_id_foreign','ne_header_invoices_user_id_foreign');
        });

        Schema::dropIfExists('ne_header_invoices');
    }
}
