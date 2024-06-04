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
        Schema::create('sch_visitor_types', function (Blueprint $table) {
            $table->id('id'); 
            $table->string('name', 100)->comment('Visitor Type')->unique();
            $table->string('description', 250)->nullable()->comment('Description');
            $table->integer('createdby')->nullable();
            $table->integer('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            
            // The timestamps() method adds created_at and updated_at columns
            $table->timestamps();
            
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_visitor_types');
    }
};
