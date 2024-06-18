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
        Schema::create('sch_fee_structure_vote_head_logs', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('structurevoteheadid')->constrained('sch_fee_structure_vote_heads')->comment('Fee Structure Votehead')->onDelete('cascade');
            $table->string('actionid')->comment('Action');
            $table->integer('createby')->nullable();
            $table->integer('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');

            $table->primary('id');
            $table->index('structurevoteheadid');
            $table->index('actionid');

            $table->foreign('actionid')->references('name')->on('sys_actions')->onDelete('cascade');
      
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_fee_structure_vote_head_logs');
    }
};
