<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('event_date')->nullable();

            // Тип события: birth, marriage, divorce, child, death, custom
            $table->string('type');

            // Короткий заголовок (например: "Развод", "Переезд в Москву")
            $table->string('title');

            // Подробное описание (опционально)
            $table->text('description')->nullable();

            // Иконка (эмодзи или имя)
            $table->string('icon')->nullable();

            // Флаг: системное событие или пользовательское
            $table->boolean('is_system')->default(false);

            $table->timestamps();

            // Индексы под таймлайн
            $table->index(['person_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_events');
    }
};
