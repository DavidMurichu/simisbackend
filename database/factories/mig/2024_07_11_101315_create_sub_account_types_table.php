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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_account_types');
    }
};
