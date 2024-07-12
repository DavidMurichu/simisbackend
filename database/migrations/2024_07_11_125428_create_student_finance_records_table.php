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
        Schema::create('student_finance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studentclasstermid')->constrained('sch_student_class_terms');
            $table->double('total_invoiced')->default(0.00)->comment('Invoiced Amount');
            $table->double('amountpaid')->default(0.00)->comment('Paid Amount');
            $table->unique(["studentclasstermid"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_finance_records');
    }
};
