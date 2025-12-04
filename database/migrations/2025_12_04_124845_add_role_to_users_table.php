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
        Schema::table('users', function (Blueprint $table) {
            // System-wide role
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            
            // Current active brand (for quick access)
            $table->foreignId('current_brand_id')->nullable()->after('role')->constrained('brands')->onDelete('set null');
            
            // Profile info
            $table->string('phone')->nullable()->after('password');
            $table->string('company')->nullable()->after('phone');
            $table->string('timezone')->default('UTC')->after('company');
            
            // Preferences
            $table->json('preferences')->nullable()->after('timezone');
            
            // Status
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->ipAddress('last_login_ip')->nullable()->after('last_login_at');
            
            // Indexes
            $table->index('role');
            $table->index('is_active');
            $table->index('current_brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_brand_id']);
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['current_brand_id']);
            $table->dropColumn([
                'role',
                'current_brand_id',
                'phone',
                'company',
                'timezone',
                'preferences',
                'is_active',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
