<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_units', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_id')->nullable();
            $table->integer('purchase_unit_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->double('purchase_factor')->nullable();
            

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
        Schema::dropIfExists('sale_units');
    }
}
