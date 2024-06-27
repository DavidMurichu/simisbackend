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
        Schema::create('sch_fee_structure_vote_heads', function (Blueprint $table) {
            $table->id('id');
            $table->string('classid')->comment('Class Name');
            $table->string('termid')->comment('Term Name');
            $table->string('voteheadid');
            $table->double('amount')->default(0.00)->comment('Vote Head Amount');
            $table->enum('status', ['0', '1'])->default('1')->comment('Activate/Deactivate');
            $table->string('remarks', 250)->nullable();
            $table->unsignedInteger('createdby')->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->unique(['classid', 'termid', 'voteheadid'], 'classid_termid_voteheadid_unique');
            $table->primary('id');
            $table->foreign('classid')->references('name')->on('sch_classes')->onDelete('cascade');
            $table->foreign('termid')->references('name')->on('sch_terms')->onDelete('cascade');
            $table->foreign('voteheadid')->references('name')->on('sch_vote_heads')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_fee_structure_vote_heads');
    }
};
