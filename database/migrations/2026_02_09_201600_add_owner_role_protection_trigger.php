<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 🔥 важно: делаем миграцию idempotent
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_owner_role_downgrade');

        DB::unprepared(<<<SQL
CREATE TRIGGER prevent_owner_role_downgrade
BEFORE UPDATE ON family_users
FOR EACH ROW
BEGIN
    IF OLD.role = 'owner' AND NEW.role <> 'owner' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Owner role cannot be changed';
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_owner_role_downgrade');
    }
};
