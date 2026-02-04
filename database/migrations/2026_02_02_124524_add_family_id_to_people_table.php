<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {

            // 👉 добавляем колонку ТОЛЬКО если её нет
            if (!Schema::hasColumn('people', 'family_id')) {
                $table->unsignedBigInteger('family_id')
                    ->nullable()
                    ->after('id');

                $table->index('family_id');

                $table->foreign('family_id')
                    ->references('id')
                    ->on('families')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {

            if (Schema::hasColumn('people', 'family_id')) {

                // 🔥 порядок важен
                $table->dropForeign(['family_id']);
                $table->dropIndex(['family_id']);
                $table->dropColumn('family_id');
            }
        });
    }
};
