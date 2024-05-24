<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender');
            $table->string('tribe');
            $table->string('nationality');
            $table->string('dob');
            $table->string('address_area');
            $table->string('town');
            $table->integer('adm_year');
            $table->foreignId('parent_id')->nullable()->constrained('guardians');
            $table->string('email')->unique();
            $table->string('image')->nullable();
            $table->string('nemis_number')->nullable()->unique();
            $table->boolean('disabled')->default(false);
            $table->boolean('is_activate')->default(false); // New field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
        
};
