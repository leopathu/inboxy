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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('email_lists')->onDelete('cascade');
            $table->string('name'); // Field name (e.g., "Company", "Phone")
            $table->string('tag'); // Field tag for merging (e.g., "[COMPANY]", "[PHONE]")
            $table->enum('type', ['text', 'number', 'date', 'dropdown', 'checkbox'])->default('text');
            $table->text('options')->nullable(); // JSON for dropdown/checkbox options
            $table->boolean('is_required')->default(false);
            $table->text('default_value')->nullable();
            $table->text('help_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('list_id');
            $table->index(['list_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
