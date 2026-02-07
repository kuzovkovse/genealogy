<?php

namespace App\Services;

use App\Models\Person;
use Carbon\Carbon;

class PersonNarrativeService
{
    public function build(Person $person): ?string
    {
        // 1️⃣ Прожитые годы (если умер)
        if ($person->birth_date && $person->death_date) {
            $birth = Carbon::parse($person->birth_date);
            $death = Carbon::parse($person->death_date);

            $years = $birth->diffInYears($death);

            if ($years > 0) {
                return $this->formatYears($years, $person->gender);
            }
        }

        // 2️⃣ Участие в войне
        if ($person->is_war_participant) {
            return 'Участник Великой Отечественной войны';
        }

        // 3️⃣ Дети
        $childrenCount = $person->children()->count();

        if ($childrenCount > 0) {
            return $this->childrenPhrase($person, $childrenCount);
        }

        // 4️⃣ Эпохи жизни (если есть годы)
        if ($person->birth_date) {
            $birthYear = Carbon::parse($person->birth_date)->year;
            $endYear = $person->death_date
                ? Carbon::parse($person->death_date)->year
                : now()->year;

            if ($endYear - $birthYear >= 60) {
                return 'Жизнь длиною в две эпохи';
            }
        }

        return null;
    }

    /* ===============================
     * helpers
     * =============================== */

    protected function formatYears(int $years, string $gender = null): string
    {
        $verb = $gender === 'female' ? 'Прожила' : 'Прожил';

        $lastDigit = $years % 10;
        $lastTwo = $years % 100;

        if ($lastTwo >= 11 && $lastTwo <= 14) {
            return "{$verb} {$years} лет";
        }

        return match ($lastDigit) {
            1 => "{$verb} {$years} год",
            2, 3, 4 => "{$verb} {$years} года",
            default => "{$verb} {$years} лет",
        };
    }

    protected function childrenPhrase(Person $person, int $count): string
    {
        $role = $person->gender === 'female' ? 'Мать' : 'Отец';

        $word = match (true) {
            $count === 1 => 'одного ребёнка',
            $count === 2 => 'двоих детей',
            $count === 3 => 'троих детей',
            $count === 4 => 'четверых детей',
            default => "{$count} детей",
        };

        return "{$role} {$word}";
    }
}
