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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic Information
            $table->string('name');
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to_email');
            $table->string('brand_logo')->nullable();
            
            // SMTP Settings
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable(); // tls, ssl, none
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();
            
            // Google reCAPTCHA
            $table->string('recaptcha_site_key')->nullable();
            $table->string('recaptcha_secret_key')->nullable();
            
            // GDPR Features
            $table->boolean('show_gdpr_options')->default(true);
            $table->boolean('only_campaigns_with_gdpr')->default(false);
            $table->boolean('only_autoresponders_with_gdpr')->default(false);
            
            // Privacy Settings
            $table->boolean('track_opens')->default(true);
            $table->boolean('track_clicks')->default(true);
            
            // Other Settings
            $table->string('default_url_query_string')->nullable();
            $table->string('test_email_subject_prefix')->nullable();
            $table->text('allowed_attachment_types')->nullable(); // JSON array
            $table->enum('default_optin_method', ['single', 'double'])->default('double');
            
            $table->timestamps();
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
