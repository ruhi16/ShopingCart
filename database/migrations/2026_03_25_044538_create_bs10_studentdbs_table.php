<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBs10StudentdbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bs10_studentdbs', function (Blueprint $table) {
            $table->id();
            $table->string('student_code')->nullable();
            $table->string('student_name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            
            $table->string('contact_number1')->nullable();
            $table->string('contact_number2')->nullable();
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('police_station')->nullable();
            $table->string('district')->nullable();
            $table->string('pin_code')->nullable();
            $table->string('nationality')->nullable();
            
            $table->string('caste')->nullable();
            $table->string('religion')->nullable();

            $table->string('admission_number')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('board_reg_no')->nullable();
            $table->string('board_roll_no')->nullable();


            $table->integer('admission_myclass_id')->nullable();
            $table->integer('admission_section_id')->nullable();
            $table->integer('admission_semester_id')->nullable();
            $table->integer('admission_session_id')->nullable();
            
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_type')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_ifsc_code')->nullable();
            
            $table->string('birth_certificate_number')->nullable();
            $table->string('aadhaar_number')->nullable();
            
            $table->string('photo_url')->nullable();
            $table->string('signature_url')->nullable();
            $table->string('birth_certificate_url')->nullable();
            $table->string('aadhaar_url')->nullable();

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
        Schema::dropIfExists('bs10_studentdbs');
    }
}
