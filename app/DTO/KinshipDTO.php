<?php

namespace App\DTO;

use App\Models\Person;

class KinshipDTO
{
    public Person $person;
    public string $kind;      // sibling | half_sibling | cousin
    public ?int $degree;      // null | 2 | 3
    public ?string $line;     // paternal | maternal | null

    public function __construct(
        Person $person,
        string $kind,
        ?int $degree = null,
        ?string $line = null
    ) {
        $this->person = $person;
        $this->kind   = $kind;
        $this->degree = $degree;
        $this->line   = $line;
    }

    /**
     * Человекочитаемый лейбл для UI
     */
    public function label(): string
    {
        return match ($this->kind) {
            'sibling'      => 'родн.',
            'half_sibling' => 'сводн.',
            'cousin'       => match ($this->degree) {
                2       => '2 юрод.',
                3       => '3 юродн.',
                default => '',
            },
            default => '',
        };
    }
}
