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
        
        Schema::create('sch_payment_terms', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('incomeno')->constrained('incomes');
            $table->string('name', 32)->comment('Name')->unique();
            $table->text('remarks')->nullable()->comment('Remarks');
            $table->unsignedBigInteger('createdby')->nullable()->comment('Created By');
            $table->unsignedBigInteger('lasteditedby')->nullable()->comment('Last Edited By');
            $table->string('ipaddress', 32)->nullable()->comment('IP Address');
            $table->string('branch_name', 100)->nullable()->comment('Branch Name');
            $table->foreignId('income_name')->nullable()->constrained('incomes')->comment('Income Name');
            $table->foreign('branch_name')->references('branch_name')->on('auth_branches');
            $table->timestamps();
        });
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

        Schema::create('sch_services', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('servicedurationid')->nullable()->constrained('sch_service_durations')->comment('School Service Duration');
            $table->foreignId('paymenttermid')->nullable()->constrained('sch_payment_terms')->comment('Payment Term');
            $table->string('name', 100)->comment('Service Name');
            $table->double('cost')->default(0.00)->comment('Service Cost');
            $table->enum('is_transport_route', ['0', '1'])->default('0')->comment('Is Transport Route');
            $table->enum('invoiced_once', ['0', '1'])->default('0')->comment('Invoiced Once?');
            $table->string('description', 32)->nullable()->comment('Description');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->foreignId('branch_id')->nullable()->constrained('auth_branches')->comment('Branch Name');
            

            $table->timestamps();
        });
        Schema::create('sch_student_services', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('studentid')->comment('Student Name');
            $table->foreignId('studentclasspromotiontermid')->nullable()->constrained('sch_student_class_terms');
            $table->foreignId('serviceid')->constrained('sch_services');
            $table->enum('status', ['0', '1'])->default('1');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->dateTime('lasteditedon')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');

            $table->foreign('studentid')->references('id')->on('sch_students');

            // Unique constraint
            $table->unique(['studentid', 'studentclasspromotiontermid', 'serviceid'],'sstu_svc_promoterm_unique');

            $table->timestamps();
        });

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
        Schema::create('sch_vote_heads', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 200)->comment('Vote Head');
            $table->string('paymentterm')->nullable()->comment('Payment Term');
            $table->string('description', 250)->nullable()->comment('Description');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->string('branch_name')->nullable()->comment('Branch Name');
            $table->foreign('paymentterm')->references('name')->on('sch_payment_terms');
            $table->foreign('branch_name')->references('branch_name')->on('auth_branches');
            $table->timestamps(); 
            $table->unique(['name']);
        });

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
            $table->primary('id');
            $table->foreign('classid')->references('name')->on('sch_classes')->onDelete('cascade');
            $table->foreign('termid')->references('name')->on('sch_terms')->onDelete('cascade');
            $table->foreign('voteheadid')->references('name')->on('sch_vote_heads')->onDelete('cascade');
            $table->unique(['classid', 'termid', 'voteheadid'], 'classid_termid_voteheadid_unique');


            $table->timestamps();
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
        
        Schema::dropIfExists('sch_payment_terms');
        Schema::dropIfExists('member_payable_arears');
        Schema::dropIfExists('sch_services');
        Schema::dropIfExists('sch_student_services');
        Schema::dropIfExists('sch_student_service_invoices');
        Schema::dropIfExists('sch_vote_heads');
        Schema::dropIfExists('sch_fee_structure_vote_heads');
        Schema::dropIfExists('sch_vote_head_invoice_details');
    }
};
