<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->string('img_01')->nullable();
            $table->string('img_02')->nullable();
            $table->string('img_03')->nullable();
            $table->string('img_04')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_active')->default(false);
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
        Schema::dropIfExists('products');
    }
}
