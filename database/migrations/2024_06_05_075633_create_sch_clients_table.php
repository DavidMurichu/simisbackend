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
        Schema::create('sch_clients', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->string('name');
            $table->string('city');
            $table->string('town');
            $table->string('neighbourhood');
            $table->string('contactname');
            $table->string('website')->nullable();
            $table->string('streetaddress');
            $table->string('mobile');
            $table->string('email');
            $table->text('remarks')->nullable();
            $table->string('createdby');
            $table->boolean('is_active')->default(true);
            $table->ipAddress('ipaddress');
            $table->string('lasteditedby')->nullable();
            $table->string('plotno')->nullable();
            $table->string('clientno')->nullable();
            $table->string('contactmobile')->nullable();
            $table->timestamps(); // Adds created_at and updated_at columns

            // Add indexes if necessary
            $table->index('email');
            $table->index('mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_clients');
    }
};
