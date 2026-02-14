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

        // =========================================
        // 🥇 1. ГОДОВЩИНА СМЕРТИ
        // =========================================
        $deathPerson = Person::withoutGlobalScopes()
            ->where('family_id', $family->id)
            ->whereNotNull('death_date')
            ->whereMonth('death_date', $today->month)
            ->whereDay('death_date', $today->day)
            ->first();

        if ($deathPerson) {
            return $this->formatDeathAnniversary($deathPerson);
        }

        // =========================================
        // 🥈 2. ВОЕННЫЕ СОБЫТИЯ
        // =========================================
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

        // =========================================
        // 🥉 3. ДЕНЬ РОЖДЕНИЯ
        // =========================================
        $birthdayPerson = Person::withoutGlobalScopes()
            ->where('family_id', $family->id)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->first();

        if ($birthdayPerson) {
            return $this->formatBirthday($birthdayPerson);
        }

        // =========================================
        // 4️⃣ ИСТОРИЧЕСКИЙ ФАКТ
        // =========================================
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
        return "🎖 Сего
