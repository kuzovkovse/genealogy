<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('person_military_documents', function (Blueprint $table) {
            $table->date('document_date')
                ->nullable()
                ->comment('Дата документа');
        });
    }

    public function down(): void
    {
        Schema::table('person_military_documents', function (Blueprint $table) {
            $table->dropColumn('document_date');
        });
    }
};
