<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memorial_candles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('visitor_name')->nullable(); // Аноним / имя
            $table->timestamp('lit_at')->useCurrent();  // 🔥 момент зажигания

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memorial_candles');
    }
};
