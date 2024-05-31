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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->boolean('view_all')->default(false)->comment('View All Branches');
            $table->string('ip_address', 45)->nullable();
            $table->string('username', 100);
            $table->string('password', 255);
            $table->unsignedBigInteger('zoneid')->nullable();
            $table->string('password_reset_code', 255)->nullable();
            $table->timestamp('password_reset_time')->nullable();
            $table->string('salt', 255)->nullable();
            $table->string('email', 100)->unique();
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('forgotten_password_code', 40)->nullable();
            $table->timestamp('forgotten_password_time')->nullable();
            $table->string('remember_code', 40)->nullable();
            $table->timestamp('created_on')->useCurrent();
            $table->timestamp('last_login')->nullable();
            $table->boolean('active');
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->unsignedBigInteger('levelid')->nullable();
            $table->unsignedBigInteger('employeeid')->nullable();
            $table->unsignedBigInteger('memberid')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('auth_branches');
            $table->timestamps();
            $table->index('email');
            $table->index('username');
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
