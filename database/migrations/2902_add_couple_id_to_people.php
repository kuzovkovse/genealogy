<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->foreignId('couple_id')
                ->nullable()
                ->after('id')
                ->constrained('couples')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropConstrainedForeignId('couple_id');
        });
    }
};
