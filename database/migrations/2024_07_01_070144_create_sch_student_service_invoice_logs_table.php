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
        Schema::create('sch_student_service_invoice_logs', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('invoiceid')->comment('Invoice');
            $table->unsignedInteger('studentserviceid')->comment('Student Service');
            $table->double('amount')->default(0.00)->comment('Service Value');
            $table->double('balance')->default(0.00)->comment('Balance Due');
            $table->unsignedInteger('createdby')->nullable();
            $table->unsignedInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->string('action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_student_service_invoice_logs');
    }
};
