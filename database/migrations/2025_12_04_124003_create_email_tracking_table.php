<?php

declare(strict_types=1);

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
        Schema::create('email_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained()->onDelete('cascade');
            $table->enum('event_type', ['open', 'click', 'bounce', 'complaint', 'unsubscribe']);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('link_url')->nullable();
            $table->string('bounce_type')->nullable();
            $table->text('bounce_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('tracked_at');
            $table->timestamps();
            
            $table->index(['campaign_id', 'event_type']);
            $table->index(['subscriber_id', 'event_type']);
            $table->index('tracked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_tracking');
    }
};
