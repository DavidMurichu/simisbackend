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
        Schema::create('auth_branches', function (Blueprint $table) {
            $table->id('id');
            $table->string('branch_name', 100)->comment('Branch Name')->unique();
            $table->string('location', 255)->nullable()->comment('Location Description');
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->unsignedInteger('createdby')->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('0')->comment('Is Active');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_branches');
    }
};
