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
        Schema::create('subscription_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('email_lists')->onDelete('cascade');
            $table->string('name');
            $table->string('identifier')->unique(); // URL-safe identifier
            $table->text('description')->nullable();
            
            // Form Settings
            $table->boolean('enable_double_optin')->default(true);
            $table->boolean('send_confirmation_email')->default(true);
            $table->boolean('is_active')->default(true);
            
            // Custom Fields Visibility
            $table->json('visible_fields')->nullable(); // Array of custom field IDs to show
            $table->json('required_fields')->nullable(); // Array of field IDs that are required
            
            // Redirect URLs
            $table->string('success_redirect_url')->nullable();
            $table->string('failure_redirect_url')->nullable();
            $table->string('confirmation_redirect_url')->nullable(); // After email confirmation
            
            // Messages
            $table->text('success_message')->nullable();
            $table->text('already_subscribed_message')->nullable();
            $table->text('confirmation_pending_message')->nullable();
            
            // Email Customization
            $table->string('confirmation_email_subject')->nullable();
            $table->text('confirmation_email_body')->nullable();
            $table->string('welcome_email_subject')->nullable();
            $table->text('welcome_email_body')->nullable();
            
            // Design Customization
            $table->string('submit_button_text')->default('Subscribe');
            $table->string('primary_color')->default('#3490dc');
            $table->text('custom_css')->nullable();
            $table->text('custom_html')->nullable(); // Additional HTML above/below form
            
            // Tracking
            $table->boolean('enable_captcha')->default(false);
            $table->string('captcha_type')->nullable(); // recaptcha, hcaptcha
            $table->string('captcha_site_key')->nullable();
            $table->string('captcha_secret_key')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('identifier');
            $table->index(['list_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_forms');
    }
};
