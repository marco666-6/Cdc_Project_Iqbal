<?php
// database/migrations/2024_01_01_000007_create_langganan_newsletter_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('langganan_newsletter', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('nama')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_token')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'verified_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('langganan_newsletter');
    }
};