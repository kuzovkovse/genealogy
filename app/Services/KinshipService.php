<?php

namespace App\Services;

use App\DTO\KinshipDTO;
use App\Models\Person;
use Illuminate\Support\Collection;

class KinshipService
{
    protected Collection $people;
    protected array $byId = [];
    protected array $childrenByCouple = [];
    protected array $couplesByPerson = [];
    protected array $parentsByPerson = [];

    public function __construct(Collection $people)
    {
        $this->people = $people;

        foreach ($people as $person) {
            $this->byId[$person->id] = $person;

            if ($person->couple_id) {
                $this->childrenByCouple[$person->couple_id][] = $person->id;
            }
        }

        foreach ($people as $person) {
            if ($person->couple_id && isset($this->byId[$person->couple_id])) {
                $couple = $this->byId[$person->couple_id];
            }
        }

        foreach ($people as $person) {
            if ($person->couple_id) {
                $this->parentsByPerson[$person->id] = $person->couple_id;
            }
        }

        // Индекс браков
        foreach ($people as $person) {
            $this->couplesByPerson[$person->id] = [];
        }

        foreach ($people as $person) {
            if ($person->couple_id) continue;

            // children уже индексированы
        }

        foreach ($people as $person) {
            $this->couplesByPerson[$person->id] = [];
        }

        // строим couples из детей
        foreach ($this->childrenByCouple as $coupleId => $childrenIds) {
            foreach ($this->people as $person) {
                if ($person->couple_id == $coupleId) {
                    $this->couplesByPerson[$person->id][] = $coupleId;
                }
            }
        }
    }

    /* ===============================
       ПРЕДКИ
    =============================== */

    public function getAncestors(Person $person, int $maxDepth = 3): Collection
    {
        $result = collect();
        $this->walkAncestors($person->id, 1, $maxDepth, null, $result);
        return $result;
    }

    protected function walkAncestors(
        int $personId,
        int $depth,
        int $maxDepth,
        ?string $line,
        Collection &$result
    ): void {
        if ($depth > $maxDepth) return;

        $parentCoupleId = $this->parentsByPerson[$personId] ?? null;
        if (!$parentCoupleId) return;

        foreach ($this->childrenByCouple[$parentCoupleId] ?? [] as $parentId) {

            if ($parentId === $personId) continue;

            $currentLine = $line ?? 'unknown';

            $result->push([
                'person' => $this->byId[$parentId],
                'depth'  => $depth,
                'line'   => $currentLine,
            ]);

            $this->walkAncestors(
                $parentId,
                $depth + 1,
                $maxDepth,
                $currentLine,
                $result
            );
        }
    }

    /* ===============================
       БРАТЬЯ / СЁСТРЫ
    =============================== */

    public function getSiblings(Person $person): Collection
    {
        $siblings = collect();

        $parentCoupleId = $this->parentsByPerson[$person->id] ?? null;
        if (!$parentCoupleId) return $siblings;

        foreach ($this->childrenByCouple[$parentCoupleId] ?? [] as $childId) {

            if ($childId === $person->id) continue;

            $siblings->push(
                new KinshipDTO($this->byId[$childId], 'sibling')
            );
        }

        return $siblings->values();
    }

    /* ===============================
       КУЗЕНЫ
    =============================== */

    public function getExtendedSiblings(Person $person, int $maxDegree = 3): Collection
    {
        $result = collect();

        $ancestors = $this->getAncestors($person, $maxDegree + 1);

        foreach ($ancestors as $ancestorData) {

            $ancestorId = $ancestorData['person']->id;
            $depth = $ancestorData['depth'];

            foreach ($this->getDescendants($ancestorId) as $descendant) {

                if ($descendant['person']->id === $person->id) continue;

                $degree = $depth + $descendant['depth'] - 2;

                if ($degree < 2 || $degree > $maxDegree) continue;

                if ($result->contains(fn($dto) => $dto->person->id === $descendant['person']->id)) {
                    continue;
                }

                $result->push(
                    new KinshipDTO(
                        $descendant['person'],
                        'cousin',
                        $degree
                    )
                );
            }
        }

        return $result->values();
    }

    protected function getDescendants(int $personId, int $depth = 1): Collection
    {
        $result = collect();

        foreach ($this->couplesByPerson[$personId] ?? [] as $coupleId) {

            foreach ($this->childrenByCouple[$coupleId] ?? [] as $childId) {

                $result->push([
                    'person' => $this->byId[$childId],
                    'depth'  => $depth,
                ]);

                $result = $result->merge(
                    $this->getDescendants($childId, $depth + 1)
                );
            }
        }

        return $result;
    }
}
