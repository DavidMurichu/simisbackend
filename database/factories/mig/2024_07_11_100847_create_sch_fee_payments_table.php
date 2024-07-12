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
        Schema::create('sch_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('studentid')->comment('Client Project');
            $table->unsignedBigInteger('studentclasspromotiontermid')->nullable()->comment('Student Class Promotion Term');
            $table->double('amountpaid', 10, 2)->default(0.00)->comment('Paid Amount');
            $table->date('paymentdate')->comment('Payment Date');
            $table->date('actual_payment_date')->nullable()->comment('Actual Slip payment Date');
            $table->string('payidby', 250)->nullable()->comment('Paid By');
            $table->unsignedBigInteger('paymentmodeid')->comment('Payment Mode');
            $table->unsignedBigInteger('bankid')->nullable()->comment('Bank');
            $table->text('description')->nullable()->comment('Description');
            $table->string('transactionno', 32)->nullable()->comment('Transaction No');
            $table->enum('is_reversed', ['0', '1'])->default('0')->comment('Is Reversed');
            $table->date('createdon')->nullable();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->unsignedBigInteger('classid')->nullable()->comment('Current Class');
            $table->unsignedTinyInteger('termid')->nullable()->comment('Current Term');
            $table->unsignedBigInteger('receiptno')->nullable()->comment('Receipt No');

            $table->foreign('studentid')->references('id')->on('sch_students');
            $table->foreign('studentclasspromotiontermid')->references('id')->on('sch_studentclassterms');
            $table->foreign('paymentmodeid')->references('id')->on('paymentmodes');
            $table->foreign('classid')->references('id')->on('sch_classes');
            $table->foreign('termid')->references('id')->on('sch_terms');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_fee_payments');
    }
};
