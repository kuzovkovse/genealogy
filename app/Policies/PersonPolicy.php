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
     * (роль проверяется middleware)
     */
    public function create(User $user): bool
    {
        return FamilyContext::has();
    }

    /**
     * Редактирование человека
     */
    public function update(User $user, Person $person): bool
    {
        return FamilyContext::belongsToFamily($person->family_id);
    }

    /**
     * Удаление человека
     */
    public function delete(User $user, Person $person): bool
    {
        // принадлежит ли человек активной семье
        if (!\App\Services\FamilyContext::belongsToFamily($person->family_id)) {
            return false;
        }

        // роль пользователя в семье: owner/editor
        return in_array(\App\Services\FamilyContext::role(), ['owner', 'editor'], true);
    }

    /**
     * Загрузка фото / документов
     */
    public function upload(User $user, Person $person): bool
    {
        return FamilyContext::belongsToFamily($person->family_id);
    }
}
