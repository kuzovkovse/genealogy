<?php

namespace App\Services;

use App\Models\Person;
use Carbon\Carbon;

class TodayInHistoryService
{
    public function build(Person $person): ?array
    {
        $today = Carbon::today();

        // 🕯 Приоритет: день смерти
        if ($person->death_date) {
            $death = Carbon::parse($person->death_date);

            if ($death->isSameDay($today)) {
                $years = $death->diffInYears($today);

                return [
                    'icon' => '🕯',
                    'title' => 'Сегодня в истории',
                    'date' => $today->translatedFormat('d F'),
                    'text' => $this->deathText($years),
                ];
            }
        }

        // 🎂 День рождения
        if ($person->birth_date) {
            $birth = Carbon::parse($person->birth_date);

            if ($birth->isSameDay($today)) {
                $years = $birth->diffInYears($today);

                return [
                    'icon' => $person->death_date ? '🕯' : '🎂',
                    'title' => 'Сегодня в истории',
                    'date' => $today->translatedFormat('d F'),
                    'text' => $this->birthText($person, $years),
                ];
            }
        }

        return null;
    }

    /* ===============================
     * Text builders
     * =============================== */

    protected function birthText(Person $person, int $years): string
    {
        if ($person->death_date) {
            return "Сегодня исполнилось бы {$years} {$this->yearsWord($years)} со дня рождения";
        }

        return "Сегодня исполняется {$years} {$this->yearsWord($years)}";
    }

    protected function deathText(int $years): string
    {
        return "День памяти — {$years} {$this->yearsWord($years)} со дня смерти";
    }

    protected function yearsWord(int $years): string
    {
        $lastDigit = $years % 10;
        $lastTwo = $years % 100;

        if ($lastTwo >= 11 && $lastTwo <= 14) {
            return 'лет';
        }

        return match ($lastDigit) {
            1 => 'год',
            2, 3, 4 => 'года',
            default => 'лет',
        };
    }
}
