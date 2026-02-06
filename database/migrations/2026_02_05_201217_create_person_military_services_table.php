<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('person_military_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->constrained()
                ->cascadeOnDelete();

            // 🔰 Базовое
            $table->string('war_name')->nullable(); // ВОВ, Первая мировая, Афган и т.д.
            $table->string('rank')->nullable();     // Звание
            $table->string('unit')->nullable();     // Воинская часть

            // 📅 Даты
            $table->year('draft_year')->nullable();     // Год призыва
            $table->year('service_end_year')->nullable(); // Год окончания службы

            // 🏅 Награды
            $table->text('awards')->nullable();

            // ⚰️ Гибель (опционально)
            $table->boolean('was_killed')->default(false);
            $table->date('death_date')->nullable();
            $table->string('burial_place')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_military_services');
    }
};
