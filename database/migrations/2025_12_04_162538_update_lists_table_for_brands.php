<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if email_lists already exists
        if (Schema::hasTable('email_lists')) {
            return;
        }
        
        // For SQLite, we need to recreate the table
        // First, get any existing data
        $existingLists = Schema::hasTable('lists') ? DB::table('lists')->get() : collect();
        
        // Drop the old table
        Schema::dropIfExists('lists');
        
        // Create new email_lists table
        Schema::create('email_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to_email')->nullable();
            $table->text('subscribe_success_message')->nullable();
            $table->text('unsubscribe_success_message')->nullable();
            $table->string('confirmation_email_subject')->nullable();
            $table->text('confirmation_email_body')->nullable();
            $table->boolean('require_confirmation')->default(true);
            $table->boolean('send_welcome_email')->default(false);
            $table->string('welcome_email_subject')->nullable();
            $table->text('welcome_email_body')->nullable();
            $table->integer('subscriber_count')->default(0);
            $table->integer('active_subscriber_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('brand_id');
            $table->index(['brand_id', 'is_active']);
        });
        
        // Restore data with brand_id = 1 (default brand)
        foreach ($existingLists as $list) {
            DB::table('email_lists')->insert([
                'id' => $list->id,
                'brand_id' => 1, // Assign to default brand
                'name' => $list->name,
                'description' => $list->description,
                'from_name' => $list->from_name,
                'from_email' => $list->from_email,
                'reply_to_email' => $list->reply_to_email,
                'subscribe_success_message' => $list->subscribe_success_message,
                'unsubscribe_success_message' => $list->unsubscribe_success_message,
                'confirmation_email_subject' => $list->confirmation_email_subject,
                'confirmation_email_body' => $list->confirmation_email_body,
                'require_confirmation' => $list->require_confirmation,
                'send_welcome_email' => $list->send_welcome_email,
                'welcome_email_subject' => $list->welcome_email_subject,
                'welcome_email_body' => $list->welcome_email_body,
                'subscriber_count' => $list->subscriber_count,
                'active_subscriber_count' => $list->active_subscriber_count,
                'is_active' => true,
                'created_at' => $list->created_at,
                'updated_at' => $list->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_lists', function (Blueprint $table) {
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['brand_id', 'is_active']);
            $table->dropSoftDeletes();
            $table->dropColumn('is_active');
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
            
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
        
        Schema::rename('email_lists', 'lists');
    }
};
