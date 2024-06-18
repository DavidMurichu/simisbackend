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
        Schema::create('member_payable_arears', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('studentid')->comment('Student Name');
            $table->foreign('studentid')->references('id')->on('sch_students')->onUpdate('cascade');
            $table->foreignId('paymenttermid')->comment('Payment Term')->constrained('sch_payment_terms');
            $table->foreignId('studentclasspromotiontermid')->nullable()->unsigned()->comment('Student Class Promotion')->constrained('sch_student_class_terms');
            $table->string('documentno', 32)->comment('Document No');
            $table->date('invoicedon')->comment('Invoiced On');
            $table->double('amount')->comment('Amount');
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->integer('createdby')->nullable()->comment('Created By');
            $table->integer('lasteditedby')->nullable()->comment('Last Edited By');
            $table->string('ipaddress', 32)->nullable()->comment('IP Address');
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            
            $table->timestamps();
    
            $table->index('paymenttermid', 'memberpayablesfkpaymentterms');
            $table->index('studentid', 'memberpayablesfkmembers');
            $table->index('studentclasspromotiontermid', 'studentclasspromotionid');
              
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_payable_arears');
    }
};
