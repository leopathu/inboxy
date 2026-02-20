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
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('optin_type', ['single', 'double'])->default('single');
            
            // Thank you email settings
            $table->boolean('thank_you_enabled')->default(false);
            $table->string('thank_you_subject')->nullable();
            $table->text('thank_you_message')->nullable();
            
            // Double optin confirmation settings
            $table->string('confirmation_subject')->nullable();
            $table->text('confirmation_message')->nullable();
            
            // Unsubscriber settings
            $table->boolean('unsubscribe_enabled')->default(false);
            $table->string('goodbye_subject')->nullable();
            $table->text('goodbye_message')->nullable();
            
            $table->timestamps();
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
