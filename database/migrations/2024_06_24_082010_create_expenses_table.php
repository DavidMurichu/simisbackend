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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 64)->comment('Name'); // VARCHAR(64) NOT NULL
            $table->foreignId('expenseType')->constrained('expense_types');// INT(11) NOT NULL
            $table->string('code', 32)->nullable()->comment('Code'); // VARCHAR(32) DEFAULT NULL
            $table->text('description')->nullable()->comment('Remarks'); // TEXT COMMENT
            $table->integer('createdby')->nullable(); // INT(11) DEFAULT NULL
            $table->dateTime('createdon')->nullable(); // DATETIME DEFAULT NULL
            $table->integer('lasteditedby')->nullable(); // INT(11) DEFAULT NULL
            $table->dateTime('lasteditedon')->nullable(); // DATETIME DEFAULT NULL
            $table->string('ipaddress', 64)->nullable(); // VARCHAR(64) DEFAULT NULL
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active'); // ENUM('0','1') DEFAULT '1'

            $table->foreignId('branch_id')->constrained('auth_branches')->onDelete('cascade'); // Foreign key constraint

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
