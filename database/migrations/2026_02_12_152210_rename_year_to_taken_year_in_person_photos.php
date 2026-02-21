<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('person_photos', function (Blueprint $table) {
            // Переименовываем колонку year → taken_year
            if (Schema::hasColumn('person_photos', 'year')
                && !Schema::hasColumn('person_photos', 'taken_year')) {

                $table->renameColumn('year', 'taken_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('person_photos', function (Blueprint $table) {
            // Откат: taken_year → year
            if (Schema::hasColumn('person_photos', 'taken_year')
                && !Schema::hasColumn('person_photos', 'year')) {

                $table->renameColumn('taken_year', 'year');
            }
        });
    }
};
