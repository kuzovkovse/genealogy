<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('memorial_candles', function (Blueprint $table) {
            $table->timestamp('lit_at')->after('visitor_name');
        });
    }

    public function down(): void
    {
        Schema::table('memorial_candles', function (Blueprint $table) {
            $table->dropColumn('lit_at');
        });
    }
};
