<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('family_invites')) {
            Schema::create('family_invites', function (Blueprint $table) {
                $table->id();

                $table->foreignId('family_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->string('email');
                $table->string('role'); // owner / editor / viewer
                $table->string('token')->unique();

                $table->foreignId('invited_by')
                    ->constrained('users')
                    ->cascadeOnDelete();

                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // deliberately empty — таблица уже существует в системе
    }
};
