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
        Schema::create('sch_payment_terms', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 32)->comment('Name')->unique();
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->unsignedBigInteger('createdby')->nullable()->comment('Created By');
            $table->unsignedBigInteger('lasteditedby')->nullable()->comment('Last Edited By');
            $table->string('ipaddress', 32)->nullable()->comment('IP Address');
            $table->string('branch_name', 100)->nullable()->comment('Branch Name');
            $table->foreignId('income_name')->constrained('incomes')->nullable()->comment('Income Name');
            $table->foreign('branch_name')->references('branch_name')->on('auth_branches');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_payment_terms');
    }
};
