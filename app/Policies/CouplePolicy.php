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
        // создавать могут owner / editor (middleware уже ограничивает)
        return FamilyContext::hasRole(['owner', 'editor']);
    }

    /**
     * Обновление связи
     */
    public function update(User $user, Couple $couple): bool
    {
        return $this->belongsToActiveFamily($couple)
            && FamilyContext::hasRole(['owner', 'editor']);
    }

    /**
     * Удаление связи
     */
    public function delete(User $user, Couple $couple): bool
    {
        // логика как у manageChildren/update (owner/editor + activeFamily)
        return $this->update($user, $couple);
    }

    /**
     * Добавление / удаление детей
     */
    public function manageChildren(User $user, Couple $couple): bool
    {
        return $this->belongsToActiveFamily($couple)
            && FamilyContext::hasRole(['owner', 'editor']);
    }

    /**
     * 🔑 Проверка принадлежности пары активной семье
     */
    protected function belongsToActiveFamily(Couple $couple): bool
    {
        $familyId = FamilyContext::id();

        return $familyId
            && (
                $couple->person1?->family_id === $familyId
                || $couple->person2?->family_id === $familyId
            );
    }
}
