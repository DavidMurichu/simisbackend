<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sch_students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->comment('Student Name');
            $table->string('admission_no', 32)->unique()->comment('Admission No');
            $table->string('nemis_number', 32)->nullable()->comment('Nemis Number');
            $table->binary('assessment_number', 32)->nullable()->comment('Assessment Number');
            $table->string('parentname', 32)->nullable()->comment('Parent Name');
            $table->string('parent_mobile', 64)->nullable()->comment('Contact Name');
            $table->string('secondary_mobile', 20)->nullable()->comment('Secondary Mobile Number');
            $table->string('dob', 20)->nullable()->comment('Date Of Birth');
            $table->binary('city', 32)->nullable()->comment('City');
            $table->string('town', 32)->nullable()->comment('Town');
            $table->text('streetaddress')->nullable()->comment('Street Address');
            $table->string('mobile', 20)->nullable()->comment('Mobile No');
            $table->string('email', 64)->nullable()->comment('Email Address');
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->unsignedInteger('createdby')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Enabled');
            $table->string('ipaddress', 32)->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->enum('teachers_student', ['0', '1'])->default('0')->comment('Teachers Pupil');
            $table->enum('deactivated', ['0', '1'])->default('0')->comment('Deactivated/Transferred');
            $table->string('deactivate_reason', 250)->comment('Reason For Deactivation');
            $table->string('fathers_name', 255)->nullable()->comment('Father\'s Name');
            $table->string('fathers_phone', 20)->nullable()->comment('Father\'s Phone');
            $table->string('mothers_name', 255)->nullable()->comment('Mother\'s Name');
            $table->string('mothers_phone', 20)->nullable()->comment('Mother\'s Phone');
            $table->string('guardians_name', 255)->nullable()->comment('Guardian\'s Name');
            $table->string('guardians_phone', 20)->nullable()->comment('Guardian\'s Phone');
            $table->string('last_school_attended', 255)->nullable()->comment('Last School Attended');
            $table->string('birth_cert_no', 32)->nullable()->comment('Birth Certificate No');
            $table->string('upi_no', 32)->nullable()->comment('Upi No');
            $table->string('academicyearid')->nullable()->comment('Join Academic Year');
            $table->string('prev_class_id');
            $table->string('current_class_id');
            $table->string('current_term_id');
            $table->string('transfer_term_id');
            $table->string('genderid');
            $table->string('branch_id');
            // Foreign Keys
            $table->foreign('academicyearid')->references('name')->on('sch_academic_years');
            $table->foreign('prev_class_id')->references('name')->on('sch_classes');
            $table->foreign('current_class_id')->references('name')->on('sch_classes');
            $table->foreign('current_term_id')->references('name')->on('sch_terms');
            $table->foreign('transfer_term_id')->references('name')->on('sch_terms');
            $table->foreign('genderid')->references('name')->on('sch_student_genders');
            $table->foreign('branch_id')->references('branch_name')->on('auth_branches');
            $table->timestamps();
            
            // Indexes
            $table->index('current_class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_students');
    }
};
