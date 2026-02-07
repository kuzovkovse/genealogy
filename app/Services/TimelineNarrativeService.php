<?php

namespace App\Services;

use App\Models\Person;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TimelineNarrativeService
{
    public function enrich(Collection $timeline, Person $person): Collection
    {
        if ($timeline->isEmpty()) {
            return $timeline;
        }

        $result = collect();
        $usedNarratives = [];

        $events = $timeline->values();

        for ($i = 0; $i < $events->count(); $i++) {
            $current = $events[$i];

            // 🔹 Перед событием — «Год, изменивший жизнь»
            if ($this->isLifeChangingYear($events, $i, $usedNarratives)) {
                $year = Carbon::parse($current['event_date'])->year;

                $key = 'life-changing-' . $year;
                if (!isset($usedNarratives[$key])) {
                    $result->push($this->narrative("{$year} — год, изменивший жизнь"));
                    $usedNarratives[$key] = true;
                }
            }

            // 🔹 Само событие
            $result->push($current);

            // 🔹 После события — «Прошло N лет»
            if (isset($events[$i + 1])) {
                $gap = $this->yearsBetween($current, $events[$i + 1]);

                if ($gap >= 10) {
                    $key = 'gap-' . $current['event_date'] . '-' . $events[$i + 1]['event_date'];

                    if (!isset($usedNarratives[$key])) {
                        $result->push(
                            $this->narrative("Прошло {$gap} лет")
                        );
                        $usedNarratives[$key] = true;
                    }
                }
            }
        }

        // 🔹 Военное время (один раз)
        if ($this->livedThroughWar($person)) {
            $insertIndex = $this->findFirstEventAfterYear($result, 1941);

            if ($insertIndex !== null) {
                $result->splice($insertIndex, 0, [
                    $this->narrative('1941–1945 — военное время')
                ]);
            }
        }

        return $result->values();
    }

    /* ===============================
     * Helpers
     * =============================== */

    protected function narrative(string $text): array
    {
        return [
            'type' => 'narrative',
            'text' => $text,
        ];
    }

    protected function yearsBetween(array $a, array $b): int
    {
        return abs(
            Carbon::parse($a['event_date'])
                ->diffInYears(Carbon::parse($b['event_date']))
        );
    }

    protected function isLifeChangingYear(Collection $events, int $index, array $used): bool
    {
        $current = $events[$index];
        $year = Carbon::parse($current['event_date'])->year;

        $count = $events->filter(function ($e) use ($year) {
            return isset($e['event_date'])
                && Carbon::parse($e['event_date'])->year === $year;
        })->count();

        return $count >= 2;
    }

    protected function livedThroughWar(Person $person): bool
    {
        if (!$person->birth_date) {
            return false;
        }

        $birthYear = Carbon::parse($person->birth_date)->year;
        $deathYear = $person->death_date
            ? Carbon::parse($person->death_date)->year
            : now()->year;

        return $birthYear <= 1945 && $deathYear >= 1941;
    }

    protected function findFirstEventAfterYear(Collection $timeline, int $year): ?int
    {
        foreach ($timeline as $index => $item) {
            if (
                isset($item['event_date'])
                && Carbon::parse($item['event_date'])->year >= $year
            ) {
                return $index;
            }
        }

        return null;
    }
}
