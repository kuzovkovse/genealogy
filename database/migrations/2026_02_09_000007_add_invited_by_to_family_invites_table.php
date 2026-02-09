<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_invites', function (Blueprint $table) {
            if (!Schema::hasColumn('family_invites', 'invited_by')) {
                $table
                    ->foreignId('invited_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('family_invites', function (Blueprint $table) {
            if (Schema::hasColumn('family_invites', 'invited_by')) {
                $table->dropForeign(['invited_by']);
                $table->dropColumn('invited_by');
            }
        });
    }
};
