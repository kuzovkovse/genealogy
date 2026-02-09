<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_audit_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('family_id');
            $table->unsignedBigInteger('actor_user_id');
            $table->unsignedBigInteger('target_user_id')->nullable();

            $table->string('action', 50);
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index('family_id');
            $table->index('actor_user_id');
            $table->index('target_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_audit_logs');
    }
};
