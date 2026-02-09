<?php

namespace App\Services;

use App\Models\Person;
use App\Models\Couple;
use Illuminate\Support\Collection;

/**
 * GenerationService
 *
 * Считает поколения внутри семьи.
 *
 * Правила:
 * 1️⃣ I поколение — люди без родителей (нет couple_id или пара не найдена)
 * 2️⃣ Дети = max(поколений родителей) + 1
 * 3️⃣ Алгоритм устойчив к неполным данным
 * 4️⃣ Все люди в итоге получают поколение
 */
class GenerationService
{
    /**
     * Построить поколения для семьи
     *
     * @param Collection<Person> $people
     * @return array<int, Collection<Person>>  [номер поколения => люди]
     */
    public function build(Collection $people): array
    {
        // --------------------------------------------
        // Подготовка
        // --------------------------------------------

        // [person_id => generation_number]
        $generationByPerson = [];

        // Все пары одним запросом
        $couples = Couple::with(['person1', 'person2', 'children'])->get()
            ->keyBy('id');

        // --------------------------------------------
        // 1️⃣ I поколение — люди без родителей
        // --------------------------------------------

        foreach ($people as $person) {
            if (!$person->couple_id || !$couples->has($person->couple_id)) {
                $generationByPerson[$person->id] = 1;
            }
        }

        // Fallback: если вообще никто не попал в I поколение
        if (empty($generationByPerson)) {
            $oldest = $people
                ->sortBy(fn ($p) => $p->birth_date ?? '9999-12-31')
                ->first();

            if ($oldest) {
                $generationByPerson[$oldest->id] = 1;
            }
        }

        // --------------------------------------------
        // 2️⃣ Распространяем поколения вниз по детям
        // --------------------------------------------

        $changed = true;

        while ($changed) {
            $changed = false;

            foreach ($couples as $couple) {

                // Определяем поколения родителей
                $parentGenerations = collect([
                    $couple->person1_id ? ($generationByPerson[$couple->person1_id] ?? null) : null,
                    $couple->person2_id ? ($generationByPerson[$couple->person2_id] ?? null) : null,
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

        // --------------------------------------------
        // 3️⃣ Graceful fallback — всё, что не определилось
        // --------------------------------------------

        foreach ($people as $person) {
            if (!isset($generationByPerson[$person->id])) {
                $generationByPerson[$person->id] = 1;
            }
        }

        // --------------------------------------------
        // 4️⃣ Группируем по поколениям
        // --------------------------------------------

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
