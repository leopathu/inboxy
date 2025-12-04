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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // API Key
            $table->string('name')->nullable(); // Friendly name
            $table->string('key', 64)->unique();
            $table->string('secret', 64)->nullable(); // Optional secret for signing
            
            // Permissions
            $table->json('permissions')->nullable(); // Specific API permissions
            $table->json('allowed_ips')->nullable(); // IP whitelist
            
            // Usage tracking
            $table->integer('requests_count')->default(0);
            $table->integer('daily_limit')->default(10000);
            $table->timestamp('last_used_at')->nullable();
            $table->ipAddress('last_used_ip')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('key');
            $table->index(['brand_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
