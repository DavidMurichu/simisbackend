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
        Schema::create('sch_teacher_on_duties', function (Blueprint $table) {
                $table->id('id');
                $table->string('duty_start')->comment('Duty Start On');
                $table->string('duty_end')->comment('Duty Ends On');
                $table->string('comments')->nullable()->comment('Comments');
                $table->integer('createdby')->nullable();
                $table->integer('lasteditedby')->nullable();
                $table->string('ipaddress', 32)->nullable();
                $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
                $table->timestamps(); // Adds created_at and updated_at columns
                $table->unsignedBigInteger('teacherid')->comment('Teacher');
                $table->foreign('teacherid')->references('id')->on('sch_teachers');
                $table->index('teacherid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_teacher_on_duties');
    }
};
