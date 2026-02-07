<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Person;
use App\Services\FamilyContext;

class PersonPolicy
{
    /**
     * Просмотр карточки человека
     */
    public function view(User $user, Person $person): bool
    {
        return FamilyContext::belongsToFamily($person->family_id);
    }

    /**
     * Создание человека
     */
    public function create(User $user): bool
    {
        return FamilyContext::hasRole(['owner', 'editor']);
    }

    /**
     * Редактирование человека
     */
    public function update(User $user, Person $person): bool
    {
        return
            FamilyContext::belongsToFamily($person->family_id)
            && FamilyContext::hasRole(['owner', 'editor']);
    }

    /**
     * Удаление человека
     */
    public function delete(User $user, Person $person): bool
    {
        return
            FamilyContext::belongsToFamily($person->family_id)
            && FamilyContext::hasRole('owner');
    }

    /**
     * Загрузка фото / документов
     */
    public function upload(User $user, Person $person): bool
    {
        return
            FamilyContext::belongsToFamily($person->family_id)
            && FamilyContext::hasRole(['owner', 'editor']);
    }
}
