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
        Schema::create('sch_teachers', function (Blueprint $table) {
            $table->id('id'); 
            $table->string('name', 300)->nullable()->comment('Full Name');
            $table->string('surname', 100)->comment('Surname');
            $table->string('firstname', 100)->comment('First Name');
            $table->string('lastname', 100)->nullable()->comment('Last Name');
            $table->string('title', 32)->nullable()->comment('Title');
            $table->string('mobileno', 20)->nullable()->comment('Active Mobile No');
            $table->string('email', 100)->nullable()->comment('Active Email Address');
            $table->text('physicaladdress')->comment('Physical Address');
            $table->integer('createdby')->nullable();
            $table->integer('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_teachers');
    }
};
