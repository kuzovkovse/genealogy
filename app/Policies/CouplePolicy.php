<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Couple;
use App\Services\FamilyContext;

class CouplePolicy
{
    /**
     * Создание связи
     */
    public function create(User $user): bool
    {
        return FamilyContext::hasRole(['owner', 'editor']);
    }

    /**
     * Обновление связи (даты, тип)
     */
    public function update(User $user, Couple $couple): bool
    {
        return
            FamilyContext::belongsToFamily($couple->family_id)
            && FamilyContext::hasRole(['owner', 'editor']);
    }

    /**
     * Удаление связи
     */
    public function delete(User $user, Couple $couple): bool
    {
        return
            FamilyContext::belongsToFamily($couple->family_id)
            && FamilyContext::hasRole('owner');
    }

    /**
     * Добавление / удаление детей
     */
    public function manageChildren(User $user, Couple $couple): bool
    {
        return
            FamilyContext::belongsToFamily($couple->family_id)
            && FamilyContext::hasRole(['owner', 'editor']);
    }
}
