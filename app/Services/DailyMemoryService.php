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
        // 🧬 События семьи
        // ===============================
        if ($family) {

            // 🕯 Память
            $deathPersons = Person::withoutGlobalScopes()
                ->where('family_id', $family->id)
                ->whereNotNull('death_date')
                ->whereMonth('death_date', $today->month)
                ->whereDay('death_date', $today->day)
                ->get();

            foreach ($deathPersons as $person) {
                $blocks[] = "🕯 *Память*\n" . $person->full_name;
            }

            // 🎂 День рождения
            $birthdays = Person::withoutGlobalScopes()
                ->where('family_id', $family->id)
                ->whereNotNull('birth_date')
                ->whereMonth('birth_date', $today->month)
                ->whereDay('birth_date', $today->day)
                ->get();

            foreach ($birthdays as $person) {

                $birthYear = Carbon::parse($person->birth_date)->year;
                $age = Carbon::now()->year - $birthYear;

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
        // 📜 Историческая календарная дата
        // ===============================
        $calendarFact = HistoricalFact::where('is_active', true)
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
        // Если нет ничего — fallback
        // ===============================
        if (empty($blocks)) {
            return $this->getHistoricalFact();
        }

        return $header . implode("\n\n", $blocks);
    }

    // ===============================
    // Fallback — обычный факт
    // ===============================
    protected function getHistoricalFact(): string
    {
        $fact = HistoricalFact::where('is_active', true)
            ->orderByRaw('COALESCE(last_shown_at, "1970-01-01") ASC')
            ->first();

        if (!$fact) {
            return "Сегодняшний день — ещё одна страница истории вашего рода.";
        }

        $fact->update([
            'last_shown_at' => now(),
        ]);

        return "📜 *Исторический факт дня*\n\n" . $fact->content;
    }
}
