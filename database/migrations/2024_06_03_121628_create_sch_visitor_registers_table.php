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
        Schema::create('sch_visitor_registers', function (Blueprint $table) {
            $table->id('id');
            $table->string('visitortype')->comment('Visitor Type');
            $table->string('name', 100)->comment('Visitor Name');
            $table->string('mobile', 15)->nullable()->comment('Mobile No');
            $table->string('gendername')->comment('Gender');
            $table->string('date_visited')->comment('Date Visited');
            $table->string('classid')->nullable()->comment('Class');
            $table->text('visiting_reason')->comment('Reason For Visiting');
            $table->text('sms')->nullable()->comment('Sms');
            $table->integer('createdby')->nullable();
            $table->integer('lasteditedby')->nullable();
            $table->string('ipaddress', 32)->nullable();
            $table->enum('is_active', ['0', '1'])->default('1')->comment('Is Active');

            // Foreign key constraints
            $table->foreign('visitortype')->references('name')->on('sch_visitor_types');
            $table->foreign('gendername')->references('name')->on('sys_genders');
            $table->foreign('classid')->references('name')->on('sch_classes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sch_visitor_registers');
    }
};
