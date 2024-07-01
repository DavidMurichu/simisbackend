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
        Schema::create('deleted_member_payerables', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('studentid');
            $table->unsignedInteger('paymenttermid')->comment('Payment Term');
            $table->unsignedInteger('studentclasspromotiontermid')->nullable()->unsigned()->comment('Student Class Promotion')->constrained('sch_student_class_terms');
            $table->string('documentno', 32)->comment('Document No');
            $table->date('invoicedon')->comment('Invoiced On');
            $table->double('amount')->comment('Amount');
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->integer('createdby')->nullable()->comment('Created By');
            $table->integer('lasteditedby')->nullable()->comment('Last Edited By');
            $table->string('ipaddress', 32)->nullable()->comment('IP Address');
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_member_payerables');
    }
};
