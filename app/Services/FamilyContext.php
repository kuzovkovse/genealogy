<?php

namespace App\Services;

use App\Models\Family;

class FamilyContext
{
    protected static ?Family $family = null;

    /**
     * Установить семью явно
     */
    public static function set(Family $family): void
    {
        self::$family = $family;
        session(['active_family_id' => $family->id]);
    }

    /**
     * Установить семью по ID (без загрузки модели)
     */
    public static function setId(int $familyId): void
    {
        session(['active_family_id' => $familyId]);
        self::$family = Family::find($familyId);
    }

    /**
     * Получить текущую семью (может быть null)
     */
    public static function get(): ?Family
    {
        if (self::$family) {
            return self::$family;
        }

        if (session()->has('active_family_id')) {
            self::$family = Family::find(session('active_family_id'));
        }

        return self::$family;
    }

    /**
     * Получить семью или 403
     */
    public static function require(): Family
    {
        $family = self::get();

        if (!$family) {
            abort(403, 'АКТИВНАЯ СЕМЬЯ НЕ ВЫБРАНА');
        }

        return $family;
    }

    /**
     * Проверка наличия контекста
     */
    public static function has(): bool
    {
        return (bool) self::get();
    }

    /**
     * ID активной семьи
     */
    public static function id(): int
    {
        return self::require()->id;
    }
}
