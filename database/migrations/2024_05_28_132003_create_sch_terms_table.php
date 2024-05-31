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
        Schema::create('sch_terms', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 50)->unique()->nullable()->comment('Term Name');
            $table->string('description', 250)->nullable()->comment('Description');
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->enum('active_term', ['0', '1'])->default('0')->comment('Is Active Term');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_terms');
    }
};
