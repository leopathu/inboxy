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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            
            // Branding
            $table->string('logo')->nullable();
            $table->string('primary_color')->default('#3490dc');
            
            // Sending limits
            $table->integer('monthly_send_limit')->default(0); // 0 = unlimited
            $table->integer('emails_sent_this_month')->default(0);
            $table->date('limit_reset_date')->nullable();
            $table->decimal('cost_per_email', 10, 6)->default(0);
            
            // AWS SES Configuration (per brand)
            $table->string('aws_access_key_id')->nullable();
            $table->string('aws_secret_access_key')->nullable();
            $table->string('aws_region')->default('us-east-1');
            $table->boolean('use_own_ses')->default(false);
            
            // SMTP Configuration (per brand)
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->boolean('use_own_smtp')->default(false);
            
            // Default sending settings
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('reply_to_email')->nullable();
            
            // Permissions
            $table->boolean('can_create_lists')->default(true);
            $table->boolean('can_create_campaigns')->default(true);
            $table->boolean('can_import_subscribers')->default(true);
            $table->boolean('can_export_data')->default(true);
            $table->boolean('can_view_reports')->default(true);
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            
            // Metadata
            $table->text('notes')->nullable();
            $table->json('settings')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
