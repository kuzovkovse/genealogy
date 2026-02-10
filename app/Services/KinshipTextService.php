<?php

namespace App\Services;

use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Collection;

class KinshipTextService
{
    public function __construct(
        protected KinshipService $kinship
    ) {}

    /**
     * «прадеда», «бабушки», «дяди»
     * — в винительном падеже
     */
    public function forUser(Person $person, User $user): string
    {
        $relation = $this->detectKinship($person);

        return $relation ?? 'родственника';
    }

    /**
     * «вашего прадеда», «вашей бабушки»
     */
    public function possessive(Person $person, User $user): string
    {
        $relation = $this->detectKinship($person);

        if (!$relation) {
            return 'вашего родственника';
        }

        return $this->withPossessive($relation, $person);
    }

    /* =====================================================
     * 🧬 CORE
     * ===================================================== */

    protected function detectKinship(Person $person): ?string
    {
        /** @var Person $me */
        $me = $this->resolveSelfPerson($person->family_id);

        if (!$me) {
            return null;
        }

        // 🧓 Предки
        $ancestors = $this->kinship->getAncestors($me, 4);

        foreach ($ancestors as $item) {
            if ($item['person']->id !== $person->id) {
                continue;
            }

            return $this->ancestorLabel(
                depth: $item['depth'],
                gender: $person->gender
            );
        }

        // 👨‍👩‍👧 Братья / сёстры
        $siblings = $this->kinship->getSiblings($me);

        foreach ($siblings as $dto) {
            if ($dto->person->id === $person->id) {
                return $person->gender === 'male'
                    ? 'брата'
                    : 'сестры';
            }
        }

        return null;
    }

    /**
     * Определяем «себя» в дереве семьи
     * (пока MVP: первый человек, созданный пользователем)
     */
    protected function resolveSelfPerson(int $familyId): ?Person
    {
        return Person::query()
            ->where('family_id', $familyId)
            ->orderBy('created_at')
            ->first();
    }

    /* =====================================================
     * 🧠 LABELS
     * ===================================================== */

    protected function ancestorLabel(int $depth, string $gender): string
    {
        return match ($depth) {
            1 => $gender === 'male' ? 'отца' : 'мать',
            2 => $gender === 'male' ? 'деда' : 'бабушки',
            3 => $gender === 'male' ? 'прадеда' : 'прабабушки',
            4 => $gender === 'male' ? 'прапрадеда' : 'прапрабабушки',
            default => 'предка',
        };
    }

    protected function withPossessive(string $relation, Person $person): string
    {
        // очень аккуратно с русским языком
        // MVP-версия, дальше можно улучшать

        if (str_ends_with($relation, 'а') || str_ends_with($relation, 'ы')) {
            return 'вашей ' . $relation;
        }

        return 'вашего ' . $relation;
    }
}
