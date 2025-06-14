<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id')->nullable();
            $table->integer('receipt_id')->nullable();
            $table->date('receipt_date')->nullable();
            $table->integer('requirment_id')->nullable();
            

            $table->integer('sale_amount_total')->nullable();
            $table->integer('sale_adjustment_total')->nullable();
            $table->integer('sale_amount_payable_total')->nullable();
            $table->boolean('is_paid')->default(false);
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
