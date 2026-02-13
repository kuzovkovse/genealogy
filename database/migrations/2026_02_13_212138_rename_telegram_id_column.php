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
        // Переименовываем telegram_id → telegram_chat_id
        if (Schema::hasColumn('users', 'telegram_id')
            && !Schema::hasColumn('users', 'telegram_chat_id')) {

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('telegram_id', 'telegram_chat_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Откат: telegram_chat_id → telegram_id
        if (Schema::hasColumn('users', 'telegram_chat_id')
            && !Schema::hasColumn('users', 'telegram_id')) {

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('telegram_chat_id', 'telegram_id');
            });
        }
    }
};
