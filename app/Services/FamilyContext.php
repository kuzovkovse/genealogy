<?php

namespace App\Services;

use App\Models\Family;
use Illuminate\Support\Facades\Auth;

class FamilyContext
{
    protected static ?Family $family = null;

    /** Есть ли активная семья */
    public static function has(): bool
    {
        try {
            static::require();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /** Получить активную семью или 403 */
    public static function require(): Family
    {
        if (self::$family) {
            return self::$family;
        }

        $user = Auth::user();

        if (!$user) {
            abort(403, 'Пользователь не авторизован');
        }

        $family = $user->families()->first();

        if (!$family) {
            abort(403, 'Семья не найдена');
        }

        return self::$family = $family;
    }

    /** ID активной семьи */
    public static function id(): ?int
    {
        return static::has() ? static::require()->id : null;
    }

    /** Роль пользователя в семье */
    public static function role(): string
    {
        $user = Auth::user();

        if (!$user) {
            return 'guest';
        }

        $family = static::require();

        $pivot = $family
            ->users()
            ->where('users.id', $user->id)
            ->first()
            ?->pivot;

        return $pivot->role ?? 'guest';
    }

    /** Принадлежит ли объект активной семье */
    public static function belongsToFamily(?int $familyId): bool
    {
        return $familyId !== null
            && static::has()
            && static::id() === $familyId;
    }

    /** Проверка ролей */
    public static function hasRole(string|array $roles): bool
    {
        $current = static::role();

        return is_array($roles)
            ? in_array($current, $roles, true)
            : $current === $roles;
    }
}
