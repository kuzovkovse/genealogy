<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔥 делаем миграцию безопасной при повторном запуске
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_owner_delete');

        DB::unprepared(<<<SQL
CREATE TRIGGER prevent_owner_delete
BEFORE DELETE ON family_users
FOR EACH ROW
BEGIN
    IF OLD.role = 'owner' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Owner cannot be removed from family';
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_owner_delete');
    }
};
