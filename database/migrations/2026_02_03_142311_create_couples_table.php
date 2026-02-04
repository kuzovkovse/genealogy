<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couples', function (Blueprint $table) {
            $table->id();

            // 👨‍👩 участники связи
            $table->unsignedBigInteger('person_1_id')->nullable();
            $table->unsignedBigInteger('person_2_id')->nullable();

            // 💍 тип связи
            // marriage | civil | parents
            $table->string('relation_type')->default('marriage');

            // 📅 даты
            $table->date('married_at')->nullable();
            $table->date('divorced_at')->nullable();

            $table->timestamps();

            // 🔗 внешние ключи
            $table->foreign('person_1_id')
                ->references('id')
                ->on('people')
                ->nullOnDelete();

            $table->foreign('person_2_id')
                ->references('id')
                ->on('people')
                ->nullOnDelete();

            // ⚡ индексы для ускорения
            $table->index('person_1_id');
            $table->index('person_2_id');
            $table->index('relation_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couples');
    }
};
