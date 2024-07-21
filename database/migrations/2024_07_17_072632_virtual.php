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
        Schema::table('sch_service_payments', function (Blueprint $table) {
            $table->dropForeign(['studentserviceid']);
            $table->foreign('studentserviceid')
                  ->references('id')->on('sch_student_services')
                  ->onDelete('cascade');
        
            $table->dropForeign(['paymentid']);
            $table->foreign('paymentid')
                  ->references('id')->on('sch_fee_payments')
                  ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sch_service_payments', function (Blueprint $table) {
            // Drop foreign keys with onDelete cascade
            $table->dropForeign(['studentserviceid']);
            $table->dropForeign(['paymentid']);

            // Add original foreign keys back (assuming they had no onDelete cascade)
            $table->foreign('studentserviceid')
                  ->references('id')->on('sch_student_services');
        
            $table->foreign('paymentid')
                  ->references('id')->on('sch_fee_payments');
        });
    }
};
