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
        Schema::create('sch_student_service_invoices', function (Blueprint $table) {
            $table->id('id');
            $table->double('amount')->default(0.00)->comment('Service Value');
            $table->double('balance')->default(0.00)->comment('Balance Due');
            $table->unsignedInteger('createdby')->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
            
            // Foreign keys
            $table->foreignId('invoiceid')->constrained('sch_fee_invoices')->onDelete("cascade");
            $table->foreignId('studentserviceid')->constrained('sch_student_services');
        });
        Schema::create('sch_vote_head_invoice_details', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('invoiceid')->comment('Invoice');
            $table->unsignedBigInteger('feestructurevoteheadsid')->comment('Feestructure Votehead');
            $table->double('amount')->default(0.00)->comment('Votehead Invoicing Amount');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps(); // Adds created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('invoiceid')->references('id')->on('sch_fee_invoices')->onDelete("cascade");
            $table->foreign('feestructurevoteheadsid')->references('id')->on('sch_fee_structure_vote_heads')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_student_service_invoices');
        Schema::dropIfExists('sch_vote_head_invoice_details');

    }
};
