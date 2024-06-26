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
            $table->string('google_oauth2_token')->nullable();
            $table->string('facebook_oauth2_token')->nullable();
            $table->string('username'); 
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string("phone_number")->nullable();
            $table->timestamp("phone_number_verified_at")->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
