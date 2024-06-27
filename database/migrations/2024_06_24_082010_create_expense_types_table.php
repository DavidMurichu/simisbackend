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
        Schema::create('expense_types', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 32)->nullable();
            $table->integer('createdby')->nullable();
            $table->date('createdon')->nullable();
            $table->integer('lasteditedby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_enabled', ['YES', 'NO'])->default('YES');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_types');
    }
};
