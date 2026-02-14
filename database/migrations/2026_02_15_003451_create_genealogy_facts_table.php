<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genealogy_facts', function (Blueprint $table) {
            $table->id();

            $table->text('content');

            // уровень важности / глубины
            $table->unsignedTinyInteger('priority')->default(5);

            // категория: archive, surname, military, documents, tradition и т.д.
            $table->string('category')->nullable();

            // активность
            $table->boolean('is_active')->default(true);

            // когда последний раз показывался
            $table->timestamp('last_shown_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genealogy_facts');
    }
};
