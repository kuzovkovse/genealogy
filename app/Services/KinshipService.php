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
}
