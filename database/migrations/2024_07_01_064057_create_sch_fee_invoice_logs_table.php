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
        Schema::create('sch_fee_invoice_logs', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('studentid')->comment('Student Name');
            $table->date('invoicedate')->comment('Invoice Date');
            $table->string('invoiceno', 32)->comment('Invoice No');
            $table->double('amount')->default(0.00)->comment('Total Invoice Amount');
            $table->double('balance')->default(0.00)->comment('Balance Due');
            $table->unsignedInteger('classid')->nullable()->comment('Current Class');
            $table->unsignedTinyInteger('termid')->nullable()->comment('Current Term');
            $table->unsignedInteger('studentclasstermsid')->nullable()->comment('Student Class Term');
            $table->enum('is_reversed', ['0', '1'])->default('0')->comment('Is Reversed');
            $table->unsignedInteger('createdby');
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->string('action')->nullable()->comment('Action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_fee_invoice_logs');
    }
};
