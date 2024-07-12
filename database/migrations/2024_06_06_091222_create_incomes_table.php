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
        Schema::create('incomes', function (Blueprint $table) {
                $table->id('id');
                $table->string('name', 64)->comment('Name');
                $table->unsignedInteger('createdby')->nullable();
                $table->unsignedInteger('lasteditedby')->nullable();
                $table->string('ipaddress', 64)->nullable();
                $table->string('branch_name')->nullable()->comment('Branch Name');
                $table->foreign('branch_name')->references('branch_name')->on('auth_branches')->onDelete('cascade');
                $table->index('branch_name');
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
