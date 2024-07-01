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
        Schema::create('sch_vote_head_invoice_detail_logs', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('invoiceid')->nullable()->comment('Invoice');
            $table->unsignedInteger('feestructurevoteheadsid')->nullable()->comment('Feestructure Votehead');
            $table->double('amount')->nullable()->comment('Votehead Invoicing Amount');
            $table->unsignedInteger('createdby')->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->nullable()->comment('Is Active');
            $table->string('action')->nullable()->comment('Action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_vote_head_invoice_detail_logs');
    }
};
