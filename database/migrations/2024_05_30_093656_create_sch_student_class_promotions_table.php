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
        Schema::create('sch_student_class_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('studentid')->comment('Student Name');
            $table->string('current_class_id')->comment('Class Name');
            $table->string('academicyear')->comment('Academic Year Name');
            $table->date('promotedon')->nullable()->comment('Date Promoted');
            $table->unsignedInteger('createdby')->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->unique(['studentid', 'current_class_id', 'academicyear'], 'unique_student_class_academic_year');  // Composite unique key
            $table->foreign('current_class_id')->references('name')->on('sch_classes');
            $table->foreign('academicyear')->references('name')->on('sch_academic_years');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_student_class_promotions');
    }
};
