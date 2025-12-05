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
        // Campaign sends tracking
        Schema::create('campaign_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['queued', 'sent', 'failed', 'bounced'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('message_id')->nullable(); // SES message ID
            $table->timestamps();
            
            $table->index(['campaign_id', 'status']);
            $table->index('subscriber_id');
            $table->index('sent_at');
        });

        // Email opens tracking
        Schema::create('campaign_opens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('opened_at');
            $table->timestamps();
            
            $table->index(['campaign_id', 'subscriber_id']);
            $table->index('opened_at');
        });

        // Email clicks tracking
        Schema::create('campaign_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->foreignId('link_id')->constrained('campaign_links')->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();
            
            $table->index(['campaign_id', 'subscriber_id']);
            $table->index('link_id');
            $table->index('clicked_at');
        });

        // Campaign links (for click tracking)
        Schema::create('campaign_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->text('url');
            $table->string('hash')->unique(); // short hash for tracking URL
            $table->integer('click_count')->default(0);
            $table->integer('unique_clicks')->default(0);
            $table->timestamps();
            
            $table->index('campaign_id');
            $table->index('hash');
        });

        // Bounces and complaints
        Schema::create('campaign_bounces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['hard', 'soft', 'complaint'])->default('hard');
            $table->text('reason')->nullable();
            $table->timestamp('bounced_at');
            $table->timestamps();
            
            $table->index(['campaign_id', 'type']);
            $table->index('subscriber_id');
        });

        // Unsubscribes from campaigns
        Schema::create('campaign_unsubscribes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->timestamp('unsubscribed_at');
            $table->timestamps();
            
            $table->index('campaign_id');
            $table->index('subscriber_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_unsubscribes');
        Schema::dropIfExists('campaign_bounces');
        Schema::dropIfExists('campaign_clicks');
        Schema::dropIfExists('campaign_links');
        Schema::dropIfExists('campaign_opens');
        Schema::dropIfExists('campaign_sends');
    }
};
