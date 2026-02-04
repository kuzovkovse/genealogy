<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->date('event_date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon', 10)->nullable();

            // системное или ручное событие
            $table->boolean('is_system')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
