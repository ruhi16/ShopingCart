<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEx30MarksEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex30_marks_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('studentcr_id')->unsigned();

            $table->integer('myclass_id')->unsigned();
            $table->integer('section_id')->unsigned();
            $table->integer('semester_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->integer('exam_detail_id')->unsigned();
            $table->integer('exam_setting_id')->unsigned();

            $table->integer('marks_obtained')->nullable();
            $table->float('marks_percentage', 10, 2)->nullable();
            $table->string('marks_grade', 10)->nullable();

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
        Schema::dropIfExists('ex30_marks_entries');
    }
}
