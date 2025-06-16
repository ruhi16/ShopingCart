<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_id')->nullable();
            $table->integer('product_id')->nullable();
            
            $table->integer('purchase_unit_id')->nullable();
            $table->integer('purchase_unit_qty')->nullable();
            $table->integer('purchase_unit_rate')->nullable();
            $table->integer('purchase_amount')->nullable();
            $table->integer('purchase_adjustment')->nullable();
            $table->integer('purchase_amount_payable')->nullable();
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
        Schema::dropIfExists('purchase_details');
    }
}
