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

            $table->foreign('acctypeid')->references('id')->on('acctypes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentmodes');
    }
};
