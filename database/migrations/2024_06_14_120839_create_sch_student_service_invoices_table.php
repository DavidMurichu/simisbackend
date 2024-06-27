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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_student_service_invoices');
    }
};
