<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonEvent extends Model
{
    protected $fillable = [
        'person_id',
        'event_date',
        'type',
        'title',
        'description',
        'icon',
        'is_system',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_system' => 'boolean',
    ];

    public const TYPES = [
        'birth'        => ['label' => 'Рождение',            'icon' => '🎂'],
        'marriage'     => ['label' => 'Брак',                'icon' => '💍'],
        'child_birth'  => ['label' => 'Рождение ребёнка',    'icon' => '👶'],
        'move'         => ['label' => 'Переезд',             'icon' => '🏠'],
        'education'    => ['label' => 'Образование',         'icon' => '🎓'],
        'service'      => ['label' => 'Служба / война',      'icon' => '🪖'],
        'death'        => ['label' => 'Смерть',              'icon' => '🕯'],
        'custom'       => ['label' => 'Другое',              'icon' => '📌'],
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function resolvedIcon(): string
    {
        return $this->icon
            ?? self::TYPES[$this->type]['icon']
            ?? '📌';
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type]['label'] ?? 'Событие';
    }
}
