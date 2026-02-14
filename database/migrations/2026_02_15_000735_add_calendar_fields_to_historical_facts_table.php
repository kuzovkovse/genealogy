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
        Schema::table('historical_facts', function (Blueprint $table) {

            // 📅 День события (1–31)
            $table->unsignedTinyInteger('event_day')
                ->nullable()
                ->after('content');

            // 📅 Месяц события (1–12)
            $table->unsignedTinyInteger('event_month')
                ->nullable()
                ->after('event_day');

            // 📅 Год события (может быть null)
            $table->unsignedSmallInteger('event_year')
                ->nullable()
                ->after('event_month');

            // 🏷 Тип события (war, reform, culture, science, religion, family)
            $table->string('type')
                ->nullable()
                ->after('event_year');

            // 🌍 Страна (например: Россия, СССР, Франция)
            $table->string('country')
                ->nullable()
                ->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historical_facts', function (Blueprint $table) {

            $table->dropColumn([
                'event_day',
                'event_month',
                'event_year',
                'type',
                'country',
            ]);
        });
    }
};
