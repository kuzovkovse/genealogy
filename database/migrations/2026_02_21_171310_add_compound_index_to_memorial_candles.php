<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memorial_candles', function (Blueprint $table) {
            $table->index(
                ['person_id', 'user_id', 'lit_at'],
                'memorial_candles_person_user_lit_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('memorial_candles', function (Blueprint $table) {
            $table->dropIndex('memorial_candles_person_user_lit_index');
        });
    }
};
