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
        Schema::create('sch_class_daily_recordings', function (Blueprint $table) {
            $table->id('id');
            $table->string('recording_date')->comment('Date Recorded');
            $table->text('general_comment')->nullable()->comment('Other Comments');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            // Foreign key constraint
            $table->foreignId('teacheronduty')->constrained('sch_teacher_on_duties')->comment('Teacher On Duty')->change();

            // Add the new foreign key constraint
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_class_daily_recordings');
    }
};
