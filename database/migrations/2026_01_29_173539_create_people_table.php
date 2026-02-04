<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('patronymic')->nullable();

            $table->enum('gender', ['male', 'female'])->nullable();

            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();

            $table->date('death_date')->nullable();
            $table->string('death_place')->nullable();

            $table->text('biography')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
