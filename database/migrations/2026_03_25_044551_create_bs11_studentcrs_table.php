<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBs11StudentcrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bs11_studentcrs', function (Blueprint $table) {
            $table->id();
            $table->integer('studentdb_id')->unsigned();
            $table->integer('roll_no')->unsigned();

            $table->integer('current_myclass_id')->nullable();
            $table->integer('current_section_id')->nullable();
            $table->integer('current_sememster_id')->nullable();
            $table->integer('current_session_id')->nullable();

            $table->integer('previous_studentcr_id')->nullable();
            $table->integer('previous_session_id')->nullable();

            $table->integer('session_id')->unsigned();
            $table->integer('school_id')->unsigned();
            
            $table->boolean('is_active')->default(true);
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('bs11_studentcrs');
    }
}
