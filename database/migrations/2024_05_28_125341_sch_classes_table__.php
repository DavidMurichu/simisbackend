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
        Schema::create('sch_classes', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 100)->unique()->comment('Class Name');
            $table->string('description', 32)->nullable()->comment('Description');
            $table->integer('createdby')->nullable();
            $table->integer('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_classes');
    }
};
