<?php

namespace App\Services;

use App\Models\Couple;
use Illuminate\Support\Collection;

class GenerationService
{
    /*
    |--------------------------------------------------------------------------
    | 🏗 БАЗОВЫЙ АЛГОРИТМ (структура с супругами)
    |--------------------------------------------------------------------------
    */

    public function build(Collection $people): array
    {
        return $this->buildWithSpouses($people);
    }

    /*
    |--------------------------------------------------------------------------
    | 👨‍👩‍👧 Семейная структура
    |--------------------------------------------------------------------------
    */

    public function buildWithSpouses(Collection $people): array
    {
        if ($people->isEmpty()) {
            return [];
        }

        $generationByPerson = [];

        $peopleIds = $people->pluck('id');

        $couples = Couple::with('children')
            ->whereIn('person_1_id', $peopleIds)
            ->orWhereIn('person_2_id', $peopleIds)
            ->orWhereHas('children', function ($q) use ($peopleIds) {
                $q->whereIn('id', $peopleIds);
            })
            ->get();

        // 1️⃣ I поколение — без родителей
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

        // 2️⃣ Распространяем вниз
        $changed = true;

        while ($changed) {
            $changed = false;

            foreach ($couples as $couple) {

                $parentGenerations = collect([
                    $generationByPerson[$couple->person_1_id] ?? null,
                    $generationByPerson[$couple->person_2_id] ?? null,
                ])->filter();

                if ($parentGenerations->isEmpty()) {
                    continue;
                }

                $childGen = $parentGenerations->max() + 1;

                foreach ($couple->children as $child) {
                    if (!isset($generationByPerson[$child->id])) {
                        $generationByPerson[$child->id] = $childGen;
                        $changed = true;
                    }
                }
            }
        }

        foreach ($people as $person) {
            if (!isset($generationByPerson[$person->id])) {
                $generationByPerson[$person->id] = 1;
            }
        }

        $result = [];

        foreach ($people as $person) {
            $gen = $generationByPerson[$person->id];
            $result[$gen] ??= collect();
            $result[$gen]->push($person);
        }

        ksort($result);

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 🧬 ЧИСТАЯ ГЕНЕАЛОГИЯ (только кровь)
    |--------------------------------------------------------------------------
    */
    public function getRootPersonId(Collection $people): ?int
    {
        if ($people->isEmpty()) {
            return null;
        }

        $root = $people
            ->sortBy(function ($p) {
                return $p->birth_date ?? '9999-12-31';
            })
            ->first();

        return $root?->id;
    }
    public function buildBloodOnly(Collection $people): array
    {
        if ($people->isEmpty()) {
            return [];
        }

        // 1️⃣ Родоначальник = самый старший
        $root = $people
            ->sortBy(fn ($p) => $p->birth_date ?? '9999-12-31')
            ->first();

        if (!$root) {
            return [];
        }

        $bloodIds = collect([$root->id]);

        $peopleIds = $people->pluck('id');

        $couples = Couple::with('children')
            ->whereIn('person_1_id', $peopleIds)
            ->orWhereIn('person_2_id', $peopleIds)
            ->get();

        // 2️⃣ Рекурсивно вниз
        $changed = true;

        while ($changed) {
            $changed = false;

            foreach ($couples as $couple) {

                if (
                    $bloodIds->contains($couple->person_1_id) ||
                    $bloodIds->contains($couple->person_2_id)
                ) {
                    foreach ($couple->children as $child) {
                        if (!$bloodIds->contains($child->id)) {
                            $bloodIds->push($child->id);
                            $changed = true;
                        }
                    }
                }
            }
        }

        // 3️⃣ Берём только кровных
        $bloodPeople = $people->whereIn('id', $bloodIds);

        // 4️⃣ Строим поколения уже ТОЛЬКО для крови
        return $this->buildWithSpouses($bloodPeople);
    }
}
