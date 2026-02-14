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

        $header = "📖 *Календарь рода*\n"
            . $today->translatedFormat('d F') . "\n\n";

        $blocks = [];

        // ===============================
        // 1️⃣ События рода (приоритет)
        // ===============================
        if ($family) {

            $deathPersons = Person::withoutGlobalScopes()
                ->where('family_id', $family->id)
                ->whereNotNull('death_date')
                ->whereMonth('death_date', $today->month)
                ->whereDay('death_date', $today->day)
                ->get();

            foreach ($deathPersons as $person) {
                $blocks[] = "🕯 *Память*\n" . $person->full_name;
            }

            $birthdays = Person::withoutGlobalScopes()
                ->where('family_id', $family->id)
                ->whereNotNull('birth_date')
                ->whereMonth('birth_date', $today->month)
                ->whereDay('birth_date', $today->day)
                ->get();

            foreach ($birthdays as $person) {

                $birthYear = Carbon::parse($person->birth_date)->year;
                $age = now()->year - $birthYear;

                if ($person->death_date) {
                    $blocks[] = "🎂 *День рождения*\n"
                        . $person->full_name
                        . "\nИсполнилось бы {$age} лет.";
                } else {
                    $blocks[] = "🎂 *День рождения*\n"
                        . $person->full_name
                        . " — {$age} лет";
                }
            }
        }

        // ===============================
        // 2️⃣ Историческая дата (строго по числу)
        // ===============================
        $calendarFact = HistoricalFact::where('is_active', true)
            ->whereNotNull('event_day')
            ->where('event_day', $today->day)
            ->where('event_month', $today->month)
            ->orderByDesc('priority')
            ->first();

        if ($calendarFact) {

            $factText = "📜 *Историческая дата*\n";

            if ($calendarFact->event_year) {
                $factText .= $calendarFact->event_year . " год\n";
            }

            $factText .= $calendarFact->content;

            $blocks[] = $factText;
        }

        // ===============================
        // 3️⃣ Если нет ничего — нейтральный ответ
        // ===============================
        if (empty($blocks)) {
            return $header . "Сегодня в истории вашего рода нет значимых дат.";
        }

        return $header . implode("\n\n", $blocks);
    }
}
