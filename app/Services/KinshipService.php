<?php

namespace App\Services;

use App\DTO\KinshipDTO;
use App\Models\Person;
use Illuminate\Support\Collection;

class KinshipService
{
    /* =========================================================
     * 🧓 ПРЕДКИ
     * ========================================================= */

    /**
     * Получить всех предков человека
     *
     * depth:
     * 1 — родители
     * 2 — деды / бабушки
     * 3 — прадеды / прабабушки
     */
    public function getAncestors(Person $person, int $maxDepth = 3): Collection
    {
        $result = collect();

        $this->walkParents(
            person: $person,
            depth: 1,
            maxDepth: $maxDepth,
            line: null,
            result: $result
        );

        return $result;
    }

    /**
     * Рекурсивный обход родительской линии
     */
    protected function walkParents(
        Person $person,
        int $depth,
        int $maxDepth,
        ?string $line,
        Collection &$result
    ): void {
        if ($depth > $maxDepth) {
            return;
        }

        $father = $person->father();
        $mother = $person->mother();

        // ОТЕЦ
        if ($father) {
            $currentLine = $line ?? 'paternal';

            $result->push([
                'person' => $father,
                'depth'  => $depth,
                'line'   => $currentLine,
            ]);

            $this->walkParents(
                person: $father,
                depth: $depth + 1,
                maxDepth: $maxDepth,
                line: $currentLine,
                result: $result
            );
        }

        // МАТЬ
        if ($mother) {
            $currentLine = $line ?? 'maternal';

            $result->push([
                'person' => $mother,
                'depth'  => $depth,
                'line'   => $currentLine,
            ]);

            $this->walkParents(
                person: $mother,
                depth: $depth + 1,
                maxDepth: $maxDepth,
                line: $currentLine,
                result: $result
            );
        }
    }

    /* =========================================================
     * 👨‍👩‍👧 БРАТЬЯ И СЁСТРЫ
     * ========================================================= */

    public function getSiblings(Person $person): Collection
    {
        $siblings = collect();

        $parentCouple = $person->parentCouple;

        if (!$parentCouple) {
            return $siblings;
        }

        // 1️⃣ Родные (та же родительская пара)
        $parentCouple->children
            ->where('id', '!=', $person->id)
            ->each(function (Person $child) use (&$siblings) {
                $siblings->push(
                    new KinshipDTO($child, 'sibling')
                );
            });

        // 2️⃣ Сводные по отцу (другие браки отца)
        if ($person->father()) {
            foreach ($person->father()->couples as $couple) {
                if ($couple->id === $parentCouple->id) {
                    continue;
                }

                foreach ($couple->children as $child) {
                    if ($child->id === $person->id) {
                        continue;
                    }

                    if ($siblings->contains(fn (KinshipDTO $dto) => $dto->person->id === $child->id)) {
                        continue;
                    }

                    $siblings->push(
                        new KinshipDTO($child, 'half_sibling')
                    );
                }
            }
        }

        // 3️⃣ Сводные по матери (другие браки матери)
        if ($person->mother()) {
            foreach ($person->mother()->couples as $couple) {
                if ($couple->id === $parentCouple->id) {
                    continue;
                }

                foreach ($couple->children as $child) {
                    if ($child->id === $person->id) {
                        continue;
                    }

                    if ($siblings->contains(fn (KinshipDTO $dto) => $dto->person->id === $child->id)) {
                        continue;
                    }

                    $siblings->push(
                        new KinshipDTO($child, 'half_sibling')
                    );
                }
            }
        }

        return $siblings->values();
    }

    /* =========================================================
     * 👨‍👩‍👧‍👦 2 И 3-ЮРОДНЫЕ
     * ========================================================= */

    public function getExtendedSiblings(Person $person, int $maxDegree = 3): Collection
    {
        $result = collect();

        // Предки текущего человека
        $myAncestors = $this->getAncestors($person, $maxDegree + 1);

        // Родные + сводные (чтобы исключить)
        $directSiblingIds = $this->getSiblings($person)
            ->pluck('person.id')
            ->toArray();

        foreach ($myAncestors as $ancestorData) {
            $ancestor = $ancestorData['person'];
            $myDepth  = $ancestorData['depth'];

            $descendants = $this->getDescendants($ancestor);

            foreach ($descendants as $descendantData) {
                $relative = $descendantData['person'];

                if ($relative->id === $person->id) {
                    continue;
                }

                if (in_array($relative->id, $directSiblingIds, true)) {
                    continue;
                }

                $relativeDepth = $descendantData['depth'];
                $degree = $myDepth + $relativeDepth - 2;

                if ($degree < 2 || $degree > $maxDegree) {
                    continue;
                }

                if ($result->contains(fn (KinshipDTO $dto) => $dto->person->id === $relative->id)) {
                    continue;
                }

                $result->push(
                    new KinshipDTO(
                        person: $relative,
                        kind: 'cousin',
                        degree: $degree
                    )
                );
            }
        }

        return $result->values();
    }

    /* =========================================================
         * РЕМАИНДЕР: текстовое описание родства между двумя людьми
         * ========================================================= */
    public function relationFor(User $user, Person $person): string
    {
        // "прадеда", "бабушки", "двоюродного деда"
    }

    /* =========================================================
     * 👶 ПОТОМКИ
     * ========================================================= */

    protected function getDescendants(Person $person, int $depth = 1, Collection $result = null): Collection
    {
        $result ??= collect();

        foreach ($person->couples as $couple) {
            foreach ($couple->children as $child) {
                $result->push([
                    'person' => $child,
                    'depth'  => $depth,
                ]);

                $this->getDescendants(
                    person: $child,
                    depth: $depth + 1,
                    result: $result
                );
            }
        }

        return $result;
    }
}
