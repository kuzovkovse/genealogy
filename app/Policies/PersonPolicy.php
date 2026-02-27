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
        // Проверяем принадлежность к активной семье
        if (!FamilyContext::belongsToFamily($person->family_id)) {
            return false;
        }

        // Разрешаем только owner и editor
        return in_array(FamilyContext::role(), ['owner', 'editor'], true);
    }

    /**
     * Загрузка фото / документов
     */
    public function upload(User $user, Person $person): bool
    {
        return FamilyContext::belongsToFamily($person->family_id);
    }
}
