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
        Schema::create('sub_account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->comment('Sub Account');
            $table->unsignedBigInteger('accounttypeid')->comment('Account Type');
            $table->integer('priority')->nullable();
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->string('ipaddress', 32)->nullable()->comment('IP Address');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });

        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->comment('Name');
            $table->unsignedBigInteger('subaccounttypeid')->nullable();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 64)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->foreign('subaccounttypeid')->references('id')->on('sub_account_types')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('paymentmodes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->comment('Name');
            $table->unsignedBigInteger('acctypeid')->nullable()->comment('Acctype');
            $table->enum('is_bank', ['0', '1'])->default('0')->comment('Is Bank');
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->dateTime('createdon')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');

            $table->foreign('acctypeid')->references('id')->on('account_types');
            $table->timestamps();
        });
        Schema::create('sch_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentclasspromotiontermid')->nullable()->comment('Student Class Promotion Term')->constrained('sch_student_class_terms');
            $table->double('amountpaid')->default(0.00)->comment('Paid Amount');
            $table->date('paymentdate')->comment('Payment Date');
            $table->date('actual_payment_date')->nullable()->comment('Actual Slip payment Date');
            $table->string('payidby', 250)->nullable()->comment('Paid By');
            $table->foreignId('paymentmodeid')->comment('Payment Mode')->constrained('paymentmodes');
            $table->foreignId('bankid')->nullable()->comment('Bank');
            $table->text('description')->nullable()->comment('Description');
            $table->string('transactionno', 32)->nullable()->comment('Transaction No');
            $table->enum('is_reversed', ['0', '1'])->default('0')->comment('Is Reversed');
            $table->date('createdon')->nullable();
            $table->foreignId('createdby')->nullable();
            $table->foreignId('lasteditedby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->unsignedInteger('studentid')->comment('Student Name');
            $table->foreign('studentid')->references('id')->on('sch_students');
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->foreignId('classid')->nullable()->comment('Current Class')->constrained('sch_classes');
            $table->unsignedTinyInteger('termid')->nullable()->comment('Current Term')->constrained('sch_terms');
            $table->foreignId('receiptno')->nullable()->comment('Receipt No');
            $table->timestamps();
        });

        Schema::create('sch_service_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentserviceid')->comment('Student Service')->constrained('sch_student_services');
            $table->foreignId('paymentid')->comment('Payment Transaction')->constrained('sch_fee_payments');
            $table->double('amount')->default(0.00)->comment('Service Amount Paid');
            $table->double('balance')->default(0.00)->comment('Balance');
            $table->date('createdon')->nullable();
            $table->foreignId('createdby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->foreignId('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });

        Schema::create('sub_account_type_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->comment('Sub Account');
            $table->unsignedBigInteger('accounttypeid')->comment('Account Type');
            $table->integer('priority')->nullable();
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->string('ipaddress', 32)->nullable()->comment('IP Address');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });

        Schema::create('account_type_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->comment('Name');
            $table->unsignedBigInteger('subaccounttypeid')->nullable();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 64)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
        
            $table->timestamps();
        });

        Schema::create('paymentmode_logs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->comment('Name');
            $table->unsignedBigInteger('acctypeid')->nullable()->comment('Acctype');
            $table->enum('is_bank', ['0', '1'])->default('0')->comment('Is Bank');
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->dateTime('createdon')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');

          
            $table->timestamps();
        });
        Schema::create('sch_fee_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('studentid')->comment('Client Project');
            $table->unsignedBigInteger('studentclasspromotiontermid')->nullable()->comment('Student Class Promotion Term');
            $table->double('amountpaid')->default(0.00)->comment('Paid Amount');
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

        
            $table->timestamps();
        });
        Schema::create('sch_service_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('studentserviceid')->comment('Student Service');
            $table->unsignedBigInteger('paymentid')->comment('Payment Transaction');
            $table->double('amount')->default(0.00)->comment('Service Amount Paid');
            $table->double('balance')->default(0.00)->comment('Balance');
            $table->date('createdon')->nullable();
            $table->unsignedBigInteger('createdby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');

           

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('sub_account_types');
        Schema::dropIfExists('account_types');
        Schema::dropIfExists('paymentmodes');
        Schema::dropIfExists('sch_fee_payments');
        Schema::dropIfExists('sch_service_payments');

        Schema::dropIfExists('sub_account_type_logs');
        Schema::dropIfExists('account_type_logs');
        Schema::dropIfExists('paymentmode_logs');
        Schema::dropIfExists('sch_fee_payment_logs');
        Schema::dropIfExists('sch_service_payment_logs');
    
    }
};
