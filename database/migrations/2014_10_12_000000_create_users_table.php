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
            $table->id();
            $table->string('username');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->unique();
            $table->string('otp_secret');
            $table->string('nationality')->default('مصري');
            $table->string('type');
            $table->bigInteger('added_by');
            $table->tinyInteger('is_block')->default(0);
            $table->tinyInteger('support_unreal_device')->default(0);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
