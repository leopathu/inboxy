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
        Schema::create('suppression_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('email')->index();
            $table->enum('reason', ['bounced', 'complained', 'unsubscribed', 'manual'])->default('manual');
            $table->text('notes')->nullable();
            $table->string('added_by')->nullable(); // User email who added it
            $table->timestamps();
            
            $table->unique(['brand_id', 'email']);
            $table->index(['brand_id', 'reason']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppression_list');
    }
};
