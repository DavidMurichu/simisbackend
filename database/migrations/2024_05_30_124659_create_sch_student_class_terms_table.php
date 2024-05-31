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
        Schema::create('sch_student_class_terms', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('studentclasspromotionid')->comment('Student Class Promotion');
            $table->string('term')->comment('Term Name');
            $table->string('classterm')->nullable()->comment('Class Term');
            $table->string('reportingdate')->nullable()->comment('Reporting Date');
            $table->unsignedInteger('createdby')->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps(); // This automatically creates `created_at` and `updated_at` columns
            $table->foreign('studentclasspromotionid')->references('id')->on('sch_student_class_promotions');
            $table->foreign('term')->references('name')->on('sch_terms');
            $table->foreign('classterm')->references('name')->on('sch_academic_year_terms');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_student_class_terms');
    }
};
