<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Services\FamilyContext;

abstract class Controller
{
    /**
     * 🔐 Проверка доступа к человеку через активную семью
     */
    protected function authorizePerson(Person $person): void
    {
        // 1️⃣ Если контекста семьи нет — пробуем восстановить из человека
        $family = FamilyContext::get();

        if (!$family) {
            if (!$person->family_id) {
                abort(403, 'У человека не указана семья');
            }

            FamilyContext::setId($person->family_id);
            $family = FamilyContext::get();
        }

        // 2️⃣ Финальная проверка
        if (!$family || $person->family_id !== $family->id) {
            abort(403);
        }
    }
}
