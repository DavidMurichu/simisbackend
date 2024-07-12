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
        Schema::create('sch_fee_reversed_invoices', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('studentid')->comment('Student Name');
            $table->string('invoicedate')->comment('Invoice Date');
            $table->string('invoiceno', 32)->unique()->comment('Invoice No');
            $table->double('amount')->default(0.00)->comment('Total Invoice Amount');
            $table->double('balance')->default(0.00)->comment('Balance Due');
            $table->foreignId('classid')->constrained('sch_classes')->nullable()->comment('Current Class');
            $table->foreignId('termid')->constrained('sch_terms')->nullable()->comment('Current Term');
            $table->foreignId('studentclasstermsid')->constrained('sch_student_class_terms')->nullable()->comment('Student Class Term');
            $table->enum('is_reversed', ['0', '1'])->default('0')->comment('Is Reversed');
            $table->unsignedInteger('createdby');
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->foreign('studentid')->references('id')->on('sch_students');
            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_fee_reversed_invoices');
    }
};
