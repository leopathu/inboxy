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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained()->onDelete('cascade');
            $table->string('email')->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->json('custom_fields')->nullable();
            $table->enum('status', ['subscribed', 'unsubscribed', 'bounced', 'complained', 'unconfirmed'])->default('unconfirmed');
            $table->string('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('complained_at')->nullable();
            $table->integer('bounce_count')->default(0);
            $table->string('bounce_type')->nullable();
            $table->text('bounce_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['list_id', 'email']);
            $table->index('status');
            $table->index(['list_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
