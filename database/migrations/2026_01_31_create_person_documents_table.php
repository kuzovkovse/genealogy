<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('person_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();

            $table->string('title')->nullable();          // Название
            $table->string('type')->nullable();           // Тип документа
            $table->integer('year')->nullable();          // Год
            $table->string('file_path');                  // Файл
            $table->text('description')->nullable();      // Описание

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_documents');
    }
};
