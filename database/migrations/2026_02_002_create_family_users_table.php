<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('family_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('role', ['owner', 'editor', 'viewer'])
                ->default('viewer');

            $table->foreignId('invited_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('joined_at')->nullable();

            $table->timestamps();

            $table->unique(['family_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_users');
    }
};
