<?php

namespace App\Services;

use App\Models\Person;
use App\Models\HistoricalFact;
use Carbon\Carbon;

class DailyMemoryService
{
    public function getTodayMessage(): string
    {
        $today = Carbon::today();

        // 1️⃣ Проверяем годовщины смерти
        $memoryPerson = Person::whereNotNull('death_date')
            ->whereMonth('death_date', $today->month)
            ->whereDay('death_date', $today->day)
            ->first();

        if ($memoryPerson) {
            return $this->formatDeathAnniversary($memoryPerson);
        }

        // 2️⃣ Если нет — берём исторический факт
        return $this->getHistoricalFact();
    }

    protected function formatDeathAnniversary(Person $person): string
    {
        $birthYear = $person->birth_date ? Carbon::parse($person->birth_date)->year : null;
        $deathYear = Carbon::parse($person->death_date)->year;

        $yearsAgo = Carbon::now()->year - $deathYear;

        $name = trim($person->last_name . ' ' . $person->first_name . ' ' . $person->middle_name);

        $lifePeriod = $birthYear
            ? "({$birthYear}–{$deathYear})"
            : "({$deathYear})";

        return "🕯 Сегодня годовщина памяти\n\n"
            . "{$name}\n"
            . "{$lifePeriod}\n\n"
            . "Прошло {$yearsAgo} лет.\n"
            . "Светлая память.";
    }

    protected function getHistoricalFact(): string
    {
        $fact = HistoricalFact::where('is_active', true)
            ->orderByRaw('COALESCE(last_shown_at, "1970-01-01") asc')
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
