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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // General Settings
            $table->string('company_name')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('en');
            
            // AWS Settings
            $table->string('aws_access_key')->nullable();
            $table->string('aws_secret_key')->nullable();
            $table->string('aws_region')->nullable();
            $table->integer('aws_sending_rate_limit')->default(14);
            
            // SMTP Settings
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();
            
            $table->timestamps();
            
            // Each user can have only one settings record
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
