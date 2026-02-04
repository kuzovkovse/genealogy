<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parent_child', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('child_id')
                ->constrained('people')
                ->cascadeOnDelete();

            // тип связи (биологический / приёмный и т.д.)
            $table->string('type')->default('biological');

            $table->timestamps();

            $table->unique(['parent_id', 'child_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_child');
    }
};
