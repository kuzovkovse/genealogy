<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('person_military_documents', function (Blueprint $table) {

            if (!Schema::hasColumn('person_military_documents', 'title')) {
                $table->string('title')->nullable()->comment('Название документа');
            }

            if (!Schema::hasColumn('person_military_documents', 'type')) {
                $table->string('type')->nullable()->comment('image | pdf');
            }

            if (!Schema::hasColumn('person_military_documents', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('person_military_documents', function (Blueprint $table) {
            $table->dropColumn(['title', 'type', 'updated_at']);
        });
    }
};
