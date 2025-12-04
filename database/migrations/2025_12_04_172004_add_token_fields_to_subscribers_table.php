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
        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('confirmation_token', 64)->nullable()->after('email');
            $table->string('unsubscribe_token', 64)->nullable()->after('confirmation_token');
            
            $table->index('confirmation_token');
            $table->index('unsubscribe_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex(['confirmation_token']);
            $table->dropIndex(['unsubscribe_token']);
            $table->dropColumn(['confirmation_token', 'unsubscribe_token']);
        });
    }
};
