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
        Schema::create('sch_services', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('servicedurationid')->nullable()->constrained('sch_service_durations')->comment('School Service Duration');
            $table->foreignId('paymenttermid')->nullable()->constrained('sch_payment_terms')->comment('Payment Term');
            $table->string('name', 100)->comment('Service Name');
            $table->double('cost')->default(0.00)->comment('Service Cost');
            $table->enum('is_transport_route', ['0', '1'])->default('0')->comment('Is Transport Route');
            $table->enum('invoiced_once', ['0', '1'])->default('0')->comment('Invoiced Once?');
            $table->string('description', 32)->nullable()->comment('Description');
            $table->unsignedBigInteger('createdby')->nullable();
            $table->unsignedBigInteger('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');
            $table->foreignId('branch_id')->nullable()->constrained('auth_branches')->comment('Branch Name');
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_services');
    }
};
