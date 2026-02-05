<?php

namespace App\Services;

use App\Models\Person;
use Illuminate\Support\Collection;

class KinshipService
{
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
    /**
     * Получить 2- и 3-юродных братьев и сестёр
     */
    public function getExtendedSiblings(Person $person, int $maxDegree = 3): Collection
    {
        $result = collect();

        // Предки текущего человека
        $myAncestors = $this->getAncestors($person, $maxDegree + 1);

        // Уже известные (родные и сводные)
        $directSiblings = $this->getSiblings($person)
            ->pluck('person.id')
            ->toArray();

        foreach ($myAncestors as $ancestorData) {
            $ancestor = $ancestorData['person'];
            $myDepth  = $ancestorData['depth'];

            // Все потомки этого предка
            $descendants = $this->getDescendants($ancestor);

            foreach ($descendants as $descendantData) {
                $relative = $descendantData['person'];

                // исключаем себя
                if ($relative->id === $person->id) {
                    continue;
                }

                // исключаем родных и сводных
                if (in_array($relative->id, $directSiblings, true)) {
                    continue;
                }

                $relativeDepth = $descendantData['depth'];

                $degree = $myDepth + $relativeDepth - 2;

                if ($degree < 2 || $degree > $maxDegree) {
                    continue;
                }

                // защита от дублей
                if ($result->contains(fn ($r) => $r['person']->id === $relative->id)) {
                    continue;
                }

                $label = match ($degree) {
                    2 => '2 юрод.',
                    3 => '3 юродн.',
                    default => null,
                };

                if ($label) {
                    $result->push([
                        'person' => $relative,
                        'type'   => 'cousin',
                        'degree' => $degree,
                        'label'  => $label,
                    ]);
                }
            }
        }

        return $result->values();
    }

    /**
     * Получить всех потомков человека
     */
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
