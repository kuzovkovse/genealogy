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

            $table->unsignedBigInteger('person_1_id');
            $table->unsignedBigInteger('person_2_id')->nullable();

            $table->string('relation_type')->default('marriage');
            $table->date('married_at')->nullable();
            $table->date('divorced_at')->nullable();

            $table->timestamps();

            $table->foreign('person_1_id')
                ->references('id')
                ->on('people')
                ->cascadeOnDelete();

            $table->foreign('person_2_id')
                ->references('id')
                ->on('people')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couples');
    }
};
