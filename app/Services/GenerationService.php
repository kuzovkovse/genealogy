<?php

namespace App\Services;

use App\Models\Couple;
use Illuminate\Support\Collection;

class GenerationService
{
    public function build(Collection $people): array
    {
        if ($people->isEmpty()) {
            return [];
        }

        $generationByPerson = [];

        $peopleIds = $people->pluck('id');

        // Загружаем ВСЕ пары, где участвуют эти люди
        $couples = Couple::with(['children'])
            ->whereIn('person_1_id', $peopleIds)
            ->orWhereIn('person_2_id', $peopleIds)
            ->orWhereHas('children', function ($q) use ($peopleIds) {
                $q->whereIn('id', $peopleIds);
            })
            ->get()
            ->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ I поколение — люди без родителей
        |--------------------------------------------------------------------------
        */

        foreach ($people as $person) {
            if (!$person->couple_id) {
                $generationByPerson[$person->id] = 1;
            }
        }

        if (empty($generationByPerson)) {
            $oldest = $people
                ->sortBy(fn ($p) => $p->birth_date ?? '9999-12-31')
                ->first();

            if ($oldest) {
                $generationByPerson[$oldest->id] = 1;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Распространяем поколения вниз
        |--------------------------------------------------------------------------
        */

        $changed = true;

        while ($changed) {
            $changed = false;

            foreach ($couples as $couple) {

                $parentGenerations = collect([
                    $couple->person_1_id ? ($generationByPerson[$couple->person_1_id] ?? null) : null,
                    $couple->person_2_id ? ($generationByPerson[$couple->person_2_id] ?? null) : null,
                ])->filter();

                if ($parentGenerations->isEmpty()) {
                    continue;
                }

                $childGeneration = $parentGenerations->max() + 1;

                foreach ($couple->children as $child) {
                    if (!isset($generationByPerson[$child->id])) {
                        $generationByPerson[$child->id] = $childGeneration;
                        $changed = true;
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Всё неопределённое — в I поколение
        |--------------------------------------------------------------------------
        */

        foreach ($people as $person) {
            if (!isset($generationByPerson[$person->id])) {
                $generationByPerson[$person->id] = 1;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Группируем
        |--------------------------------------------------------------------------
        */

        $result = [];

        foreach ($people as $person) {
            $gen = $generationByPerson[$person->id];

            $result[$gen] ??= collect();
            $result[$gen]->push($person);
        }

        ksort($result);

        return $result;
    }
}
