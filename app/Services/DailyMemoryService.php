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

        if ($family) {

            // 🕯 Память
            $deathPersons = Person::withoutGlobalScopes()
                ->where('family_id', $family->id)
                ->whereNotNull('death_date')
                ->whereMonth('death_date', $today->month)
                ->whereDay('death_date', $today->day)
                ->get();

            foreach ($deathPersons as $person) {
                $blocks[] = "🕯 *Память*\n"
                    . $person->full_name;
            }

            // 🎂 День рождения
            $birthdays = Person::withoutGlobalScopes()
                ->where('family_id', $family->id)
                ->whereNotNull('birth_date')
                ->whereMonth('birth_date', $today->month)
                ->whereDay('birth_date', $today->day)
                ->get();

            foreach ($birthdays as $person) {
                $age = Carbon::now()->year - Carbon::parse($person->birth_date)->year;

                $blocks[] = "🎂 *День рождения*\n"
                    . $person->full_name . " — {$age} лет";
            }
        }

        // 📜 Историческая дата
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

        if (empty($blocks)) {
            return $this->getHistoricalFact();
        }

        return $header . implode("\n\n", $blocks);
    }

}
