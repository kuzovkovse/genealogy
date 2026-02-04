<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_invites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('family_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('email')->nullable();

            $table->enum('role', ['editor', 'viewer'])
                ->default('viewer');

            $table->string('token')->unique();

            $table->foreignId('invited_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_invites');
    }
};
