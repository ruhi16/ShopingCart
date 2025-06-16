<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id')->nullable();
            $table->integer('product_id')->nullable();
            
            $table->integer('sale_unit_id')->nullable();
            $table->integer('sale_unit_qty')->nullable();
            $table->integer('sale_unit_rate')->nullable();
            $table->integer('sale_amount')->nullable();
            $table->integer('sale_adjustment')->nullable();
            $table->integer('sale_amount_payable')->nullable();
            
            

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
        Schema::dropIfExists('sale_details');
    }
}
