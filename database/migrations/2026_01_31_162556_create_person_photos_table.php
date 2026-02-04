<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_photos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->string('image_path');      // путь к файлу
            $table->string('title')->nullable();       // "Свадьба", "Армия"
            $table->text('description')->nullable();   // подпись / история
            $table->integer('year')->nullable();       // 1988, 2014 и т.п.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_photos');
    }
};
