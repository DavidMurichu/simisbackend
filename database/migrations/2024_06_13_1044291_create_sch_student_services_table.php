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
        Schema::create('sch_student_services', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('studentid')->comment('Student Name');
            $table->foreignId('studentclasspromotiontermid')->nullable()->constrained('sch_student_class_terms');
            $table->foreignId('serviceid')->constrained('sch_services');
            $table->enum('status', ['0', '1'])->default('1');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->foreign('studentid')->references('id')->on('sch_students');

            // Unique constraint
            $table->unique(['studentid', 'studentclasspromotiontermid', 'serviceid'],'sstu_svc_promoterm_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_student_services');
    }
};
