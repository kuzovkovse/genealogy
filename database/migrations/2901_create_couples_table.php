<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('couples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_1_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('person_2_id')->constrained('people')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['person_1_id', 'person_2_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couples');
    }
};
