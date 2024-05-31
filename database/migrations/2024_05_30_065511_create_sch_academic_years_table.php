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
        Schema::create('sch_academic_years', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 100)->comment('Academic Year')->unique();
            $table->string('description', 250)->nullable()->comment('Description');
            $table->string('startdate')->nullable()->comment('Expected Start Date');
            $table->string('enddate')->nullable()->comment('Expected End Date'); // Fixed typo: 'endate' to 'enddate'
            $table->integer('createdby')->nullable();
            $table->integer('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->string('year', 32);
            $table->foreign('year')->references('year')->on('sys_years');
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_academic_years');
    }
};
