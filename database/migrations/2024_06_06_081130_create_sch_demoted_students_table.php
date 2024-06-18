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
        Schema::create('sch_demoted_students', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('studentclasstermid')->comment('Academic Yr Term');
                $table->string('datedemoted')->comment('Date Demoted');
                $table->string('current_class_id')->comment('Demoted To Class');
                $table->string('current_term_id')->comment('Term Demoted To');
                $table->string('academicyear')->comment('Year Demoted');
                $table->string('demotedby')->nullable()->comment('Demoted By');
    
                $table->unsignedInteger('createdby')->nullable();
                $table->unsignedInteger('lasteditedby')->nullable();
                $table->string('ipaddress', 32)->nullable();
                $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
                $table->timestamps();
                $table->foreign('studentclasstermid')->references('id')->on('sch_student_class_terms');
                $table->foreign('current_class_id')->references('name')->on('sch_classes');
                $table->foreign('current_term_id')->references('name')->on('sch_terms');
                $table->foreign('academicyear')->references('name')->on('sch_academic_years');
    
                $table->index('studentclasstermid');
                $table->index('current_class_id');
                $table->index('current_term_id');
                $table->index('academicyear');
            });
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_demoted_students');
    }
};
