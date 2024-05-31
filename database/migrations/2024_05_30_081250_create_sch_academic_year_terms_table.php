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
        Schema::create('sch_academic_year_terms', function (Blueprint $table) {
            $table->id('id');
            $table->string('academicyear');
            $table->string('term');
            $table->string('name', 100)->unique();
            $table->string('description', 250)->nullable();
            $table->date('startdate')->nullable();
            $table->date('enddate')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->foreign('academicyear')->references('name')->on('sch_academic_years');
            $table->foreign('term')->references('name')->on('sch_terms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_academic_year_terms');
    }
};
