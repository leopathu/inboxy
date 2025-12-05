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
        Schema::table('campaigns', function (Blueprint $table) {
            // Campaign Types
            $table->enum('type', ['regular', 'autoresponder', 'trigger'])->default('regular')->after('list_id');
            
            // Template & Editor
            $table->foreignId('template_id')->nullable()->after('reply_to_email');
            $table->json('attachments')->nullable()->after('plain_text_content');
            
            // Sending Configuration
            $table->enum('send_to', ['all', 'subscribed', 'unconfirmed'])->default('subscribed')->after('segments');
            $table->integer('throttle_rate')->nullable()->after('send_to'); // emails per hour
            $table->boolean('enable_throttle')->default(false)->after('throttle_rate');
            $table->text('test_emails')->nullable()->after('enable_throttle'); // for test sends
            
            // Autoresponder Configuration
            $table->integer('delay_value')->nullable()->after('test_emails'); // 1, 2, 3, etc
            $table->enum('delay_unit', ['minutes', 'hours', 'days', 'weeks'])->nullable()->after('delay_value');
            $table->timestamp('trigger_date')->nullable()->after('delay_unit'); // specific date trigger
            $table->string('trigger_event')->nullable()->after('trigger_date'); // 'subscribe', 'purchase', etc
            $table->boolean('is_active')->default(true)->after('trigger_event'); // for autoresponders
            
            // Additional Tracking
            $table->integer('failed_sends')->default(0)->after('emails_unsubscribed');
            
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'template_id',
                'attachments',
                'send_to',
                'throttle_rate',
                'enable_throttle',
                'test_emails',
                'delay_value',
                'delay_unit',
                'trigger_date',
                'trigger_event',
                'is_active',
                'failed_sends',
            ]);
        });
    }
};
