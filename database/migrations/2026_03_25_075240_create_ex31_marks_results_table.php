<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEx31MarksResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ex31_marks_results', function (Blueprint $table) {
            $table->id();
            $table->integer('studentcr_id')->unsigned();

            $table->integer('myclass_id')->unsigned();
            $table->integer('section_id')->unsigned();
            $table->integer('semester_id')->unsigned();
                        
            $table->integer('total_marks_obtained')->nullable();
            $table->integer('total_marks_examed')->nullable();

            $table->float('total_marks_percentage', 10, 2)->nullable();
            $table->string('total_marks_grade', 10)->nullable();
            $table->integer('total_no_of_d_grade_obtained')->unsigned();

            $table->string('result_status')->nullable();
            $table->string('result_remarks')->nullable();
            
            $table->integer('next_myclass_id')->unsigned();
            $table->integer('next_section_id')->unsigned();
            $table->boolean('is_admitted_to_new_class')->default(false);

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
        Schema::dropIfExists('ex31_marks_results');
    }
}
