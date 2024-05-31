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
        Schema::create('sys_years', function (Blueprint $table) {
            $table->id();
            $table->string('year', 32)->comment('Year Name')->unique();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sys_years');
    }
};
