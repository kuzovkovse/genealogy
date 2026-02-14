<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historical_facts', function (Blueprint $table) {
            $table->id();

            // Текст факта
            $table->text('content');

            // Категория (history / family / war / archive и т.п.)
            $table->string('category')->nullable();

            // Приоритет (чем выше — тем чаще выбирается)
            $table->integer('priority')->default(1);

            // Когда последний раз показывался
            $table->timestamp('last_sent_at')->nullable();

            // Активен ли факт
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historical_facts');
    }
};
