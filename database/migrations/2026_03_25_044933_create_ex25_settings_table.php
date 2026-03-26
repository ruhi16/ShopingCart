<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEx25SettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex25_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            $table->integer('myclass_id')->unsigned();
            $table->integer('section_id')->unsigned();
            $table->integer('semester_id')->unsigned();
            
            $table->integer('exam_detail_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            
            $table->integer('full_mark')->unsigned();
            $table->integer('pass_mark')->unsigned();
            $table->integer('time_in_minutes')->unsigned();

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
        Schema::dropIfExists('ex25_settings');
    }
}
