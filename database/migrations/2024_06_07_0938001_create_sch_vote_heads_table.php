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
        Schema::create('sch_vote_heads', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 200)->comment('Vote Head');
            $table->string('paymentterm')->nullable()->comment('Payment Term');
            $table->string('description', 250)->nullable()->comment('Description');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->string('branch_name')->nullable()->comment('Branch Name');
            $table->foreign('paymentterm')->references('name')->on('sch_payment_terms');
            $table->foreign('branch_name')->references('branch_name')->on('auth_branches');
            $table->timestamps(); 
            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_vote_heads');
    }
};
