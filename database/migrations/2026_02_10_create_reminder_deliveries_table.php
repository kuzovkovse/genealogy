<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminder_deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('family_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('channel'); // email | telegram
            $table->string('type');    // birthday | memorial | system

            $table->text('title');
            $table->text('body');

            $table->timestamp('scheduled_for');
            $table->timestamp('sent_at')->nullable();

            $table->string('status')->default('pending'); // pending | sent | failed
            $table->text('error')->nullable();

            $table->timestamps();

            $table->unique([
                'family_id',
                'person_id',
                'type',
                'scheduled_for',
                'channel'
            ], 'unique_reminder_delivery');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_deliveries');
    }
};
