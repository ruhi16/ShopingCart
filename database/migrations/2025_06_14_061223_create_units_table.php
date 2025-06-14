<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->nullable();         // 1 Pouch
            $table->double('unit_amount')->nullable();  // 10
            $table->string('unit_title')->nullable();   // ML
            $table->double('unit_price')->nullable();   // Rs 2.5
            
            $table->string('detail')->nullable();   

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
        Schema::dropIfExists('units');
    }
}
