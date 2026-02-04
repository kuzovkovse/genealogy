<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Couple;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class TreeController extends Controller
{
    public function show(Person $person): JsonResponse
    {
        $root = $this->findRootFather($person);

        return response()->json([
            'roots' => [
                $this->buildPerson($root)
            ]
        ]);
    }

    /**
     * Корень = мужчина по отцовской линии
     */
    private function findRootFather(Person $person): Person
    {
        $current = $person;

        while (true) {
            $parentCouple = Couple::whereHas('children', function ($q) use ($current) {
                $q->where('people.id', $current->id);
            })->first();

            if (!$parentCouple || !$parentCouple->person1) {
                break;
            }

            $current = $parentCouple->person1;
        }

        return $current;
    }

    /**
     * ЧЕЛОВЕК
     */
    private function buildPerson(Person $person): array
    {
        $couples = Couple::where('person_1_id', $person->id)
            ->orWhere('person_2_id', $person->id)
            ->with(['person1', 'person2', 'children'])
            ->orderByRaw('married_at IS NULL, married_at')
            ->get();

        return [
            'type' => 'person',
            'id' => $person->id,
            'name' => trim("{$person->last_name} {$person->first_name}"),
            'gender' => $person->gender,
            'photo' => $this->avatar($person),
            'birth_date' => $person->birth_date,
            'death_date' => $person->death_date,
            'is_dead' => (bool) $person->death_date,

            'children' => $couples->map(
                fn ($couple) => $this->buildCouple($couple)
            )->toArray()
        ];
    }

    /**
     * БРАК
     */
    private function buildCouple(Couple $couple): array
    {
        $start = $couple->married_at
            ? Carbon::parse($couple->married_at)
            : null;

        $end = $couple->divorced_at
            ? Carbon::parse($couple->divorced_at)
            : ($couple->person2?->death_date
                ? Carbon::parse($couple->person2->death_date)
                : now());

        return [
            'type' => 'couple',
            'id' => 'couple-' . $couple->id,

            'years' => [
                'from' => $start?->year,
                'to' => $couple->divorced_at ? $end->year : null,
                'duration' => $start ? round($start->floatDiffInYears($end), 1) : null,
            ],

            // 👨‍👩 родители
            'husband' => $couple->person1
                ? $this->simplePerson($couple->person1)
                : null,

            'wife' => $couple->person2
                ? $this->simplePerson($couple->person2)
                : null,

            // 👶 дети
            'children' => $couple->children->map(
                fn ($child) => $this->buildPerson($child)
            )->toArray(),
        ];
    }

    /**
     * Короткая версия человека (для брака)
     */
    private function simplePerson(Person $person): array
    {
        return [
            'type' => 'person',
            'id' => $person->id,
            'name' => trim("{$person->last_name} {$person->first_name}"),
            'gender' => $person->gender,
            'photo' => $this->avatar($person),
            'birth_date' => $person->birth_date,
            'death_date' => $person->death_date,
            'is_dead' => (bool) $person->death_date,
        ];
    }

    private function avatar(Person $person): string
    {
        if ($person->photo) {
            return asset('storage/' . $person->photo);
        }

        $initials = mb_strtoupper(
            mb_substr($person->first_name, 0, 1) .
            mb_substr($person->last_name ?? '', 0, 1)
        );

        return route('avatar', [
            'name' => $initials ?: '?',
            'gender' => $person->gender,
        ]);
    }
}
