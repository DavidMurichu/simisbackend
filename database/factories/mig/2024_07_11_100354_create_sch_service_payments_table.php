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
        Schema::create('sch_service_payments', function (Blueprint $table) {
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

            $table->foreign('studentserviceid')->references('id')->on('sch_student_services');
            $table->foreign('paymentid')->references('id')->on('sch_feepayments');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_service_payments');
    }
};
