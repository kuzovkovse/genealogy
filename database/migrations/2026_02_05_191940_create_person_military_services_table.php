<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_military_services', function (Blueprint $table) {
            $table->id();

            // 🔗 Связь с человеком
            $table->foreignId('person_id')
                ->constrained()
                ->cascadeOnDelete();

            // 🪖 Тип войны
            $table->string('war_type', 50)
                ->comment('ww1, ww2, afghanistan, chechnya, other');

            // 📅 Служба
            $table->year('draft_year')->nullable();          // год призыва
            $table->string('rank')->nullable();              // звание
            $table->date('service_start')->nullable();       // начало службы
            $table->date('service_end')->nullable();         // окончание службы
            $table->string('unit')->nullable();              // воинская часть

            // 🎖 Награды и документы
            $table->text('awards')->nullable();               // список наград (пока текст)
            $table->json('documents')->nullable();            // военные документы

            // ⚰️ Гибель
            $table->boolean('is_killed')->default(false);
            $table->date('killed_date')->nullable();
            $table->text('burial_place')->nullable();

            // 📝 Дополнительно
            $table->text('notes')->nullable();

            $table->timestamps();

            // ⚡ Индексы
            $table->index('war_type');
            $table->index('is_killed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_military_services');
    }
};
