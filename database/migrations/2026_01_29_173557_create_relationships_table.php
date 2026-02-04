<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relationships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('related_person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->enum('type', [
                'father',
                'mother',
                'child',
                'spouse'
            ]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relationships');
    }
};
