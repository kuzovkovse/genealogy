<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {

            // 📍 Где похоронен
            $table->string('burial_cemetery')->nullable()->after('death_date');
            $table->string('burial_city')->nullable()->after('burial_cemetery');

            // 🗂 Участок / ряд / место
            $table->string('burial_place')->nullable()->after('burial_city');

            // 🧭 Как найти
            $table->text('burial_description')->nullable()->after('burial_place');

            // 🗺 Координаты (опционально)
            $table->decimal('burial_lat', 10, 7)->nullable()->after('burial_description');
            $table->decimal('burial_lng', 10, 7)->nullable()->after('burial_lat');
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn([
                'burial_cemetery',
                'burial_city',
                'burial_place',
                'burial_description',
                'burial_lat',
                'burial_lng',
            ]);
        });
    }
};
