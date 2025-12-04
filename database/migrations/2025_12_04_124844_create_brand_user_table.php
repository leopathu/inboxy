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
        Schema::create('brand_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Role within this brand
            $table->enum('role', ['owner', 'manager', 'user'])->default('user');
            
            // Specific permissions for this brand
            $table->boolean('can_manage_lists')->default(true);
            $table->boolean('can_manage_campaigns')->default(true);
            $table->boolean('can_manage_subscribers')->default(true);
            $table->boolean('can_view_reports')->default(true);
            $table->boolean('can_manage_settings')->default(false);
            $table->boolean('can_manage_users')->default(false);
            
            $table->timestamps();
            
            // Unique constraint - user can be in brand only once
            $table->unique(['brand_id', 'user_id']);
            
            // Indexes
            $table->index('user_id');
            $table->index('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_user');
    }
};
