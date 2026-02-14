<?php

namespace App\Services;

use App\Models\Person;
use App\Models\HistoricalFact;
use App\Models\User;
use Carbon\Carbon;

class DailyMemoryService
{
    public function getMessageForUser(User $user): string
    {
        $today = Carbon::today();
        $family = $user->families()->first();

        if (!$family) {
            return $this->getHistoricalFact();
        }

        // 🥇 1. Годовщина смерти
        $deathPerson = Person::withoutGlobalScopes()
            ->where('family_id', $family->id)
            ->whereNotNull('death_date')
            ->whereMonth('death_date', $today->month)
            ->whereDay('death_date', $today->day)
            ->first();

        if ($deathPerson) {
            return $this->formatDeathAnniversary($deathPerson);
        }

        // 🥈 2. Военные участники (если ДР совпадает)
        $warPerson = Person::withoutGlobalScopes()
            ->where('family_id', $family->id)
            ->where('is_war_participant', true)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->first();

        if ($warPerson) {
            return $this->formatWarMemory($warPerson);
        }

        // 🥉 3. День рождения
        $birthdayPerson = Person::withoutGlobalScopes()
            ->where('family_id', $family->id)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->first();

        if ($birthdayPerson) {
            return $this->formatBirthday($birthdayPerson);
        }

        // 4️⃣ Исторический факт
        return $this->getHistoricalFact();
    }

    protected function formatDeathAnniversary(Person $person): string
    {
        $birthYear = $person->birth_date
            ? Carbon::parse($person->birth_date)->year
            : null;

        $deathYear = Carbon::parse($person->death_date)->year;
        $yearsAgo = Carbon::now()->year - $deathYear;

        $lifePeriod = $birthYear
            ? "({$birthYear}–{$deathYear})"
            : "({$deathYear})";

        return "🕯 Сегодня годовщина памяти\n\n"
            . $person->full_name . "\n"
            . $lifePeriod . "\n\n"
            . "Прошло {$yearsAgo} лет.\n"
            . "Светлая память.";
    }

    protected function formatWarMemory(Person $person): string
    {
        return "🎖 Памятная дата участника войны\n\n"
            . $person->full_name . "\n\n"
            . "Участник Великой Отечественной войны.\n"
            . "Помним и гордимся.";
    }

    protected function formatBirthday(Person $person): string
    {
        $birthYear = Carbon::parse($person->birth_date)->year;
        $age = Carbon::now()->year - $birthYear;

        if ($person->death_date) {
            return "🎂 Сегодня день рождения\n\n"
                . $person->full_name . "\n"
                . "Родился в {$birthYear} году.\n"
                . "Исполнилось бы {$age} лет.";
        }

        return "🎂 Сегодня день рождения\n\n"
            . $person->full_name . "\n"
            . "Исполняется {$age} лет.";
    }

    protected function getHistoricalFact(): string
    {
        $fact = HistoricalFact::where('is_active', true)
            ->orderByRaw('COALESCE(last_shown_at, \"1970-01-01\") ASC')
            ->first();

        if (!$fact) {
            return "Сегодняшний день — ещё одна страница истории вашего рода.";
        }

        $fact->update([
            'last_shown_at' => now(),
        ]);

        return "📜 Исторический факт дня\n\n" . $fact->content;
    }
}
