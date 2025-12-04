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
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lists');
    }
};
