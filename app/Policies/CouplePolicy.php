<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Couple;
use App\Services\FamilyContext;

class CouplePolicy
{
    /**
     * Создание связи
     * (роль проверяется middleware)
     */
    public function create(User $user): bool
    {
        return FamilyContext::has();
    }

    /**
     * Обновление связи (даты, тип)
     */
    public function update(User $user, Couple $couple): bool
    {
        return FamilyContext::belongsToFamily($couple->family_id);
    }

    /**
     * Удаление связи
     */
    public function delete(User $user, Couple $couple): bool
    {
        return FamilyContext::belongsToFamily($couple->family_id);
    }

    /**
     * Добавление / удаление детей
     */
    public function manageChildren(User $user, Couple $couple): bool
    {
        return FamilyContext::belongsToFamily($couple->family_id);
    }
}
